<?php
/**
 * This file contains all supportive functions for the Newsletter plugin
 */

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Elgg\Email;
use Elgg\Database\QueryBuilder;
use Elgg\Email\Address;

/**
 * Start the commandline to send a newsletter
 * This is offloaded because it could take a while and/or resources
 *
 * @param Newsletter $entity Newsletter entity to be processed
 *
 * @return void
 */
function newsletter_start_commandline_sending(Newsletter $entity) {
	
	// prepare commandline settings
	$settings = [
		'entity_guid' => $entity->guid,
		'host' => $_SERVER['HTTP_HOST'],
		'memory_limit' => ini_get('memory_limit'),
		'secret' => newsletter_generate_commanline_secret($entity->guid),
	];
	if (isset($_SERVER['HTTPS'])) {
		$settings['https'] = $_SERVER['HTTPS'];
	}
	
	// ini settings
	$ini_param = '';
	$ini_file = php_ini_loaded_file();
	if (!empty($ini_file)) {
		$ini_param = "-c {$ini_file} ";
	}
	
	// which script to run
	$script_location = dirname(dirname(__FILE__)) . '/procedures/cli.php';
	
	// convert settings to commandline params
	$query_string = http_build_query($settings, '', ' ');
	
	// start the correct commandline
	if (PHP_OS === 'WINNT') {
		pclose(popen('start /B php ' . $ini_param . $script_location . ' ' . $query_string, 'r'));
	} else {
		exec('php ' . $ini_param . $script_location . ' ' . $query_string . ' > /dev/null &');
	}
}

/**
 * Generate a secret to be used for validation
 *
 * @param int $entity_guid guid of the newsletter
 *
 * @return boolean|string
 */
function newsletter_generate_commanline_secret($entity_guid) {
	
	$entity_guid = (int) $entity_guid;
	if ($entity_guid < 1) {
		return false;
	}
	
	$plugin = elgg_get_plugin_from_id('newsletter');
	
	$hmac = elgg_build_hmac([
		$plugin->guid,
		$entity_guid,
		$plugin->time_created,
	]);
	return $hmac->getToken();
}

/**
 * Validate if the provided secret is correct
 *
 * @param int    $entity_guid guid of the newsletter entity
 * @param string $secret      secret code to be validated
 *
 * @return boolean
 */
function newsletter_validate_commandline_secret($entity_guid, $secret) {
	
	$entity_guid = (int) $entity_guid;
	if ($entity_guid < 1 || empty($secret)) {
		return false;
	}
	
	$correct_secret = newsletter_generate_commanline_secret($entity_guid);
	if (empty($correct_secret) || $correct_secret !== $secret) {
		return false;
	}
	
	return true;
}

/**
 * Process the newsletter for a given guid
 *
 * @param int $entity_guid guid of the newsletter
 *
 * @return void
 */
function newsletter_process($entity_guid) {
	
	$entity_guid = (int) $entity_guid;
	if ($entity_guid < 1) {
		return;
	}
	
	// ignore access
	$entity = elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity_guid) {
		return get_entity($entity_guid);
	});
	
	// is this a Newsletter
	if (!$entity instanceof Newsletter) {
		return;
	}
	
	// sending could take a while
	set_time_limit(0);
	
	elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity) {
		
		$logging = ['start_time' => time()];
		
		$site = elgg_get_site_entity();
		$container = $entity->getContainerEntity();
		
		// set newsletter status to sending
		$entity->status = 'sending';
		$entity->start_time = $logging['start_time'];
		
		// get the recipients
		
		// basic set of user selection options
		$basic_user_options = [
			'type' => 'user',
			'limit' => false,
			'batch' => true,
			'selects' => [
				function (QueryBuilder $qb, $main_alias) {
					$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'email');
					
					return "{$metadata}.value AS email";
				},
			],
			'callback' => false,
			'wheres' => [],
			'metadata_name_value_pairs' => [],
		];
		
		// include banned users?
		if ((bool) !elgg_get_plugin_setting('include_banned_users', 'newsletter')) {
			$basic_user_options['metadata_name_value_pairs'][] = [
				'name' => 'banned',
				'value' => 'no',
			];
		}
		
		// include users without settings
		if (elgg_get_plugin_setting('include_existing_users', 'newsletter') === 'yes') {
			// yes, so exclude blocked
			$basic_user_options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($site) {
				// general blacklist
				$blocked = $qb->subquery('entity_relationships');
				$blocked->select('guid_one')
					->where($qb->compare('relationship', '=', NewsletterSubscription::GENERAL_BLACKLIST, ELGG_VALUE_STRING))
					->andWhere($qb->compare('guid_two', '=', $site->guid, ELGG_VALUE_GUID));
				
				return $qb->compare("{$main_alias}.guid", 'not in', $blocked->getSQL());
			};
			$basic_user_options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($container) {
				// blacklist / unsubscribed
				$blocked = $qb->subquery('entity_relationships');
				$blocked->select('guid_one')
					->where($qb->compare('relationship', '=', NewsletterSubscription::BLACKLIST, ELGG_VALUE_STRING))
					->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
				
				return $qb->compare("{$main_alias}.guid", 'not in', $blocked->getSQL());
			};
		} else {
			// no, so subscription is required
			$basic_user_options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($container) {
				$subbed = $qb->subquery('entity_relationships');
				$subbed->select('guid_one')
					->where($qb->compare('relationship', '=', NewsletterSubscription::SUBSCRIPTION, ELGG_VALUE_STRING))
					->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
				
				return $qb->compare("{$main_alias}.guid", 'in', $subbed->getSQL());
			};
		}
		
		$filtered_recipients = [
			'users' => [],
			'emails' => [],
		];
		$recipients = $entity->getRecipients();
		if (empty($recipients)) {
			// no recipients so report error
			$entity->status = 'sent';
			
			return;
		}
		
		// recipients is an array consisting of:
		// - user_guids: 	individual users
		// - group_guids:	groups to send the content to
		// - emails:		individual email addresses
		// - subscribers:	(int) whether or not to add the subscribers of the container
		// - members:		(int) whether or not to aad the member of the container
		$user_guids = elgg_extract('user_guids', $recipients);
		if (!empty($user_guids)) {
			if (!is_array($user_guids)) {
				$user_guids = [$user_guids];
			}
			
			// convert to a format we can use
			$options = $basic_user_options;
			$options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($user_guids) {
				return $qb->compare("{$main_alias}.guid", 'in', $user_guids, ELGG_VALUE_GUID);
			};
			
			$users = elgg_get_entities($options);
			/* @var $row \stdClass */
			foreach ($users as $row) {
				$filtered_recipients['users'][(int) $row->guid] = $row->email;
			}
		}
		
		$group_guids = elgg_extract('group_guids', $recipients);
		if (!empty($group_guids)) {
			if (!is_array($group_guids)) {
				$group_guids = [$group_guids];
			}
			
			$options = $basic_user_options;
			
			$options['relationship_guid'] = $group_guids;
			$options['relationship'] = 'member';
			$options['inverse_relationship'] = true;
			
			$users = elgg_get_entities($options);
			/* @var $row \stdClass */
			foreach ($users as $row) {
				$filtered_recipients['users'][(int) $row->guid] = $row->email;
			}
		}
		
		$subscribers = elgg_extract('subscribers', $recipients);
		if (!empty($subscribers)) {
			$subscribers = newsletter_get_subscribers($container);
			
			$filtered_recipients['users'] += $subscribers['users'];
			$filtered_recipients['emails'] = array_merge($filtered_recipients['emails'], $subscribers['emails']);
		}
		
		$members = elgg_extract('members', $recipients);
		if (!empty($members)) {
			$options = $basic_user_options;
			
			if ($container instanceof ElggGroup) {
				$options['relationship'] = 'member';
				$options['relationship_guid'] = $container->guid;
				$options['inverse_relationship'] = true;
			}
			
			$users = elgg_get_entities($options);
			/* @var $row \stdClass */
			foreach ($users as $row) {
				$filtered_recipients['users'][(int) $row->guid] = $row->email;
			}
		}
		
		$emails = elgg_extract('emails', $recipients);
		if (!empty($emails)) {
			if (!is_array($emails)) {
				$emails = [$emails];
			}
			
			// get blocked users
			$options = [
				'type' => 'user',
				'limit' => false,
				'batch' => true,
				'selects' => [
					function (QueryBuilder $qb, $main_alias) {
						$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'email');
						
						return "{$metadata}.value AS email";
					},
				],
				'metadata_name_value_pairs' => [
					[
						'name' => 'email',
						'value' => $emails,
						'case_sensitive' => false,
					],
				],
				'wheres' => [
					function (QueryBuilder $qb, $main_alias) use ($site, $container) {
						$wheres = [];
						
						// general blacklist
						$general = $qb->subquery('entity_relationships');
						$general->select('guid_one')
							->where($qb->compare('relationship', '=', NewsletterSubscription::GENERAL_BLACKLIST, ELGG_VALUE_STRING))
							->andWhere($qb->compare('guid_two', '=', $site->guid, ELGG_VALUE_GUID));
						
						$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $general->getSQL());
						
						// blacklist / unsubscribed
						$blacklist = $qb->subquery('entity_relationships');
						$blacklist->select('guid_one')
							->where($qb->compare('relationship', '=', NewsletterSubscription::BLACKLIST, ELGG_VALUE_STRING))
							->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
						
						$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $blacklist->getSQL());
						
						return $qb->merge($wheres, 'OR');
					},
				],
				'callback' => false,
			];
			
			// include banned users?
			if ((bool) !elgg_get_plugin_setting('include_banned_users', 'newsletter')) {
				$options['metadata_name_value_pairs'][] = [
					'name' => 'banned',
					'value' => 'no',
				];
			}
			
			$blocked_emails = [];
			
			$users = elgg_get_entities($options);
			/* @var $row \stdClass */
			foreach ($users as $row) {
				$blocked_emails[] = $row->email;
			}
			
			$emails = array_diff($emails, $blocked_emails);
			
			if (!empty($emails)) {
				// get blocked emails
				$options = [
					'type' => 'object',
					'subtype' => NewsletterSubscription::SUBTYPE,
					'limit' => false,
					'batch' => true,
					'selects' => [
						function (QueryBuilder $qb, $main_alias) {
							$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'title');
							
							return "{$metadata}.value AS email";
						},
					],
					'metadata_name_value_pairs' => [
						[
							'name' => 'title',
							'value' => $emails,
							'case_sensitive' => false,
						],
					],
					'wheres' => [
						function (QueryBuilder $qb, $main_alias) use ($site, $container) {
							$wheres = [];
							
							// general blacklist
							$general = $qb->subquery('entity_relationships');
							$general->select('guid_one')
								->where($qb->compare('relationship', '=', NewsletterSubscription::GENERAL_BLACKLIST, ELGG_VALUE_STRING))
								->andWhere($qb->compare('guid_two', '=', $site->guid, ELGG_VALUE_GUID));
							
							$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $general->getSQL());
							
							// blacklist / unsubscribed
							$blacklist = $qb->subquery('entity_relationships');
							$blacklist->select('guid_one')
								->where($qb->compare('relationship', '=', NewsletterSubscription::BLACKLIST, ELGG_VALUE_STRING))
								->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
							
							$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $blacklist->getSQL());
							
							return $qb->merge($wheres, 'OR');
						},
					],
					'callback' => false,
				];
				
				$blocked_emails = [];
				
				$subscriptions = elgg_get_entities($options);
				/* @var $row \stdClass */
				foreach ($subscriptions as $row) {
					$blocked_emails[] = $row->email;
				}
				
				$emails = array_diff($emails, $blocked_emails);
				
				if (!empty($emails)) {
					$filtered_recipients['emails'] = array_merge($filtered_recipients['emails'], $emails);
				}
			}
		}
		
		// get newsletter content
		if ($entity->subject) {
			$message_subject = $entity->subject;
		} else {
			$message_subject = elgg_echo('newsletter:subject', [$container->getDisplayName(), $entity->getDisplayName()]);
		}
		$message_plaintext_content = elgg_echo('newsletter:plain_message', [$entity->getURL()]);
		
		$message_html_content = elgg_view_layout('newsletter', ['entity' => $entity]);
		
		// proccess all recipients
		if ((elgg_get_plugin_setting('custom_from', 'newsletter') === 'yes') && !empty($entity->from)) {
			// from is validated to a valid email address in the newsletter save action
			$from = new Address($entity->from, $container->getDisplayName());
		} else {
			// default to the container email address
			$from = new Address($site->getEmailAddress(), $container->getDisplayName());
		}
		
		// set default send options
		$send_options = [
			'from' => $from,
			'subject' => $message_subject,
			'body' => $message_plaintext_content,
		];
		
		$save_recipient_logging = function ($recipient_log) use ($entity, &$logging) {
			$logging['recipients'][] = $recipient_log;
			
			$entity->saveLogging($logging);
		};
		
		$is_banned = function (int $guid) use ($filtered_recipients) {
			static $cache;
			
			if (elgg_get_plugin_setting('include_banned_users', 'newsletter')) {
				// banned users are allowed
				return false;
			}
			
			if (!isset($cache)) {
				$cache = elgg_get_metadata([
					'type' => 'user',
					'guids' => array_keys($filtered_recipients['users']),
					'metadata_name_value_pairs' => [
						[
							'name' => 'banned',
							'value' => 'yes',
						],
					],
					'callback' => function($row) {
						return (int) $row->entity_guid;
					},
				]);
			}
			
			return in_array($guid, $cache);
		};
		
		foreach ($filtered_recipients as $type => $recipients) {
			if (empty($recipients)) {
				continue;
			}
			
			foreach ($recipients as $id => $recipient) {
				$recipient_log = [
					'type' => $type,
					'email' => $recipient,
					'time' => date(DATE_RFC1123),
					'timestamp' => time(),
					'status' => false,
				];
				
				// create individual footer for unsubscribe link
				if ($type == 'users') {
					$recipient_log['guid'] = $id;
					
					if ($is_banned($id)) {
						$save_recipient_logging($recipient_log);
						continue;
					}
					
					$unsubscribe_link = newsletter_generate_unsubscribe_link($container, $id);
				} else {
					$unsubscribe_link = newsletter_generate_unsubscribe_link($container, $recipient);
				}
				
				// place the unsubscribe link in the message
				$unsubscribe_link = elgg_normalize_url($unsubscribe_link);
				$message_html_content_user = str_ireplace("{unsublink}", $unsubscribe_link, $message_html_content);
				
				// replace the online link for logged out users to add an emailadres
				if ($type !== 'users') {
					$online_link = $entity->getURL();
					$new_online_link = elgg_http_add_url_query_elements($online_link, [
						'e' => $recipient,
					]);
					
					$message_html_content_user = str_ireplace($online_link, $new_online_link, $message_html_content_user);
				}
				
				// add URL postfix to all internal links
				$message_html_content_user = newsletter_apply_url_postfix($message_html_content_user, $entity);
				
				// send mail
				$send_options['to'] = $recipient;
				$send_options['params']['html_message'] = $message_html_content_user;
				
				$email = Email::factory($send_options);
				
				try {
					$recipient_log['status'] = elgg_send_email($email);
				} catch (Exception $e) {
					// some error during sending
				}
				
				if ($recipient_log['status'] && !empty($recipient_log['guid'])) {
					$entity->addRelationship($recipient_log['guid'], Newsletter::SEND_TO);
				}
				
				// add to logging
				$save_recipient_logging($recipient_log);
			}
		}
		
		$logging['end_time'] = time();
		
		$entity->saveLogging($logging);
		
		// set newsletter status to done
		$entity->status = 'sent';
		
		// needed to trigger the update ts so we now something changed
		$entity->save();
		
		// send status notification
		if (newsletter_is_email_address($entity->status_notification)) {
			$email = Email::factory([
				'to' => $entity->status_notification,
				'from' => $from,
				'subject' => elgg_echo('newsletter:status_notification:subject'),
				'body' => elgg_echo('newsletter:status_notification:message', [
					$entity->getDisplayName(),
					$entity->getURL(),
				]),
			]);
			
			elgg_send_email($email);
		}
	});
}

/**
 * Format an e-mail recipient to be listed in the recipient autocomplete or listing
 *
 * @param string $recipient email address
 *
 * @return array Contains, of false on failure
 * 					- type : email
 * 					- label: used in the autocomplete dropdown
 * 					- html: used in the listing view
 * 					- value: email address
 */
function newsletter_format_email_recipient($recipient) {
	
	if (!newsletter_is_email_address($recipient)) {
		return false;
	}
	
	// switch viewtype
	$view_type = elgg_get_viewtype();
	elgg_set_viewtype('default');
	
	$name = elgg_format_element('span', [], elgg_echo('newsletter:recipients:email') . ': ') . $recipient;
	$label = elgg_view_image_block('', $name, ['class' => 'elgg-autocomplete-item']);
	
	$delete_icon = elgg_view_icon('delete', ['class' => 'elgg-autocomplete-item-remove']);
	
	$content = elgg_view('input/hidden', ['name' => 'emails[]', 'value' => $recipient]);
	$content .= elgg_view_image_block('', $recipient, ['image_alt' => $delete_icon]);
	
	// restore viewtype
	elgg_set_viewtype($view_type);
	
	return [
		'type' => 'email',
		'label' => $label,
		'html' => elgg_format_element('li', ['class' => 'elgg-item'], $content),
	];
}

/**
 * Get all the people that subscribed to the newsletter of this container
 *
 * @param ElggEntity $container Which container
 * @param bool       $count	    Return just a count, not the actual subscribers
 *
 * @return false|int|array
 */
function newsletter_get_subscribers(ElggEntity $container, $count = false) {
	
	if (!$container instanceof ElggSite && !$container instanceof ElggGroup) {
		return false;
	}
	
	// get the subscribers
	if (!$count) {
		$result = [
			'users' => [],
			'emails' => [],
		];
		
		// get all subscribed community members
		$user_emails = elgg_get_metadata([
			'type' => 'user',
			'metadata_names' => ['email'],
			'limit' => false,
			'batch' => true,
			'relationship' => NewsletterSubscription::SUBSCRIPTION,
			'relationship_guid' => $container->guid,
			'inverse_relationship' => true,
		]);
		/* @var $user_email ElggMetadata */
		foreach ($user_emails as $user_email) {
			$result['users'][$user_email->entity_guid] = $user_email->value;
		}
		
		// check the email subscriptions
		$result['emails'] = elgg_get_entities([
			'type' => 'object',
			'subtype' => NewsletterSubscription::SUBTYPE,
			'selects' => [
				function (QueryBuilder $qb, $main_alias) {
					$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'title');
					
					return "{$metadata}.value AS title";
				},
			],
			'limit' => false,
			'relationship' => NewsletterSubscription::SUBSCRIPTION,
			'relationship_guid' => $container->guid,
			'inverse_relationship' => true,
			'callback' => function($row) {
				return $row->title;
			},
		]);
	} else {
		// get all subscribed community members
		$result = elgg_get_entities([
			'type' => 'user',
			'count' => true,
			'relationship' => NewsletterSubscription::SUBSCRIPTION,
			'relationship_guid' => $container->guid,
			'inverse_relationship' => true,
		]);
		
		// check the email subscriptions
		$result += elgg_get_entities([
			'type' => 'object',
			'subtype' => NewsletterSubscription::SUBTYPE,
			'count' => true,
			'relationship' => NewsletterSubscription::SUBSCRIPTION,
			'relationship_guid' => $container->guid,
			'inverse_relationship' => true,
		]);
	}
	
	return $result;
}

/**
 * Check if a user is subscribed to a container entity
 *
 * @param ElggUser   $user   The user to check
 * @param ElggEntity $entity The container entity to check against
 *
 * @return boolean True => the user has a subscription, false => no subscription or error
 */
function newsletter_check_user_subscription(ElggUser $user, ElggEntity $entity) {
	
	if (!$entity instanceof \ElggSite && !$entity instanceof \ElggGroup) {
		return false;
	}
	
	// include all users
	if (elgg_get_plugin_setting('include_existing_users', 'newsletter') === 'yes') {
		// exclude if blocked
		return !((bool) check_entity_relationship($user->guid, NewsletterSubscription::BLACKLIST, $entity->guid));
	} else {
		// only if opt-in
		return (bool) check_entity_relationship($user->guid, NewsletterSubscription::SUBSCRIPTION, $entity->guid);
	}
}

/**
 * Add a subscription for an user to a container
 *
 * @param ElggUser   $user                  The user to subscribe
 * @param ElggEntity $entity                The container entity to subscribe to
 * @param bool       $cleanup_general_block Remove the generic block all (default: true)
 *
 * @return boolean true on success else false
 */
function newsletter_subscribe_user(ElggUser $user, ElggEntity $entity, $cleanup_general_block = true) {
	
	if (!$entity instanceof ElggSite && !$entity instanceof ElggGroup) {
		return false;
	}
	
	// check if subscribed
	if (!check_entity_relationship($user->guid, NewsletterSubscription::SUBSCRIPTION, $entity->guid)) {
		// not yet, so add
		$result = add_entity_relationship($user->guid, NewsletterSubscription::SUBSCRIPTION, $entity->guid);
	} else {
		$result = true;
	}
	
	// remove blocklist relation
	remove_entity_relationship($user->guid, NewsletterSubscription::BLACKLIST, $entity->guid);
	
	// remove general blocklist
	if ($cleanup_general_block) {
		$site = elgg_get_site_entity();
		remove_entity_relationship($user->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid);
	}
	
	// check if on email blacklist
	$subscription = newsletter_get_subscription($user->email);
	
	if (!empty($subscription)) {
		$subscription->removeRelationship($entity, NewsletterSubscription::BLACKLIST);
	}
	
	return $result;
}

/**
 * Add a subscription for an email address to a container
 *
 * @param string     $email  The email address to add to the subscriptions
 * @param ElggEntity $entity The container entity to subscribe to
 *
 * @return boolean true on success else false
 */
function newsletter_subscribe_email($email, ElggEntity $entity) {
	
	if (!newsletter_is_email_address($email)) {
		return false;
	}
	
	if (!$entity instanceof ElggSite && !$entity instanceof ElggGroup) {
		return false;
	}
	
	// check if email belongs to existing user
	$users = get_user_by_email($email);
	if (!empty($users)) {
		return newsletter_subscribe_user($users[0], $entity);
	}

	// check if email address exists in the system
	$subscription = newsletter_get_subscription($email);
	
	if (empty($subscription)) {
		$subscription = new NewsletterSubscription();
		$subscription->title = $email;
		
		if (!$subscription->save()) {
			return false;
		}
	}
	
	// subscribe
	$result = (bool) $subscription->addRelationship($entity->guid, NewsletterSubscription::SUBSCRIPTION);
	
	// remove blocklist relation
	remove_entity_relationship($subscription->guid, NewsletterSubscription::BLACKLIST, $entity->guid);
	
	// remove general blocklist
	$site = elgg_get_site_entity();
	remove_entity_relationship($subscription->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid);
	
	return $result;
}

/**
 * Remove a subscription for an user to a container
 *
 * @param ElggUser   $user   The user to unsubscribe
 * @param ElggEntity $entity The container entity to unsubscribe from
 *
 * @return boolean true on success else false
 */
function newsletter_unsubscribe_user(ElggUser $user, ElggEntity $entity) {
	
	if (!$entity instanceof ElggSite && !$entity instanceof ElggGroup) {
		return false;
	}
	
	// remove subscription
	remove_entity_relationship($user->guid, NewsletterSubscription::SUBSCRIPTION, $entity->guid);
	
	// check if on email subscriptionlist
	$subscription = newsletter_get_subscription($user->email);
	
	if (!empty($subscription)) {
		$subscription->removeRelationship($entity->guid, NewsletterSubscription::SUBSCRIPTION);
	}
	
	// check if blocked
	if (!check_entity_relationship($user->guid, NewsletterSubscription::BLACKLIST, $entity->guid)) {
		// not yet, so add
		return add_entity_relationship($user->guid, NewsletterSubscription::BLACKLIST, $entity->guid);
	}
	
	return true;
}

/**
 * Remove a subscription for an email address to a container
 *
 * @param string     $email  The email address to remove from the subscriptions
 * @param ElggEntity $entity The container entity to unsubscribe from
 *
 * @return boolean true on success else false
 */
function newsletter_unsubscribe_email($email, ElggEntity $entity) {
	
	if (!newsletter_is_email_address($email)) {
		return false;
	}
	
	if ((!$entity instanceof \ElggSite) && (!$entity instanceof \ElggGroup)) {
		return false;
	}
	
	// check if not existing user
	$users = get_user_by_email($email);
	if (!empty($users)) {
		// existing user
		return newsletter_unsubscribe_user($users[0], $entity);
	}

	// email address
	$subscription = newsletter_get_subscription($email);
	
	if (empty($subscription)) {
		$subscription = new NewsletterSubscription();
		$subscription->title = $email;
		
		if (!$subscription->save()) {
			return false;
		}
	}
	
	// remove existing subscription (if any)
	$subscription->removeRelationship($entity->getGUID(), NewsletterSubscription::SUBSCRIPTION);
	
	// add to blocked list
	return $subscription->addRelationship($entity->getGUID(), NewsletterSubscription::BLACKLIST);
}

/**
 * Custom callback for elgg_get_* function to return a subset of information about an user
 *
 * @param stdClass $row A database row
 *
 * @return array contains [guid] => email
 *
 * @see elgg_get_entities()
 */
function newsletter_user_row_to_subscriber_info($row) {
	return [
		'guid' => (int) $row->guid,
		'email' => $row->email,
	];
}

/**
 * A different interpretation of is_email_address()
 * because PHP doesn't always correctly verify email addresses
 *
 * @param string $address The email address to check
 *
 * @return bool true if email, false otherwise
 *
 * @see is_email_address()
 * @see filter_var()
 */
function newsletter_is_email_address($address) {
	
	if (empty($address)) {
		return false;
	}
	
	$regexpr = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
	
	return (bool) preg_match($regexpr, $address);
}

/**
 * Generate an URL so the recipient can directly unsubscribe from a newsletter
 *
 * @param ElggEntity $container Which newsletter container (ElggSite or ElggGroup)
 * @param string|int $recipient The user_guid or email address of the recipient
 *
 * @return bool|string The unsubscribe link or false on failure
 */
function newsletter_generate_unsubscribe_link(ElggEntity $container, $recipient) {

	if (!$container instanceof \ElggSite && !$container instanceof \ElggGroup) {
		return false;
	}
	
	$params = [
		'guid' => $container->guid,
	];
	
	if (!empty($recipient)) {
		$code = newsletter_generate_unsubscribe_code($container, $recipient);
		
		if (is_numeric($recipient)) {
			// recipient is an user_guid
			$params['u'] = $recipient;
			$params['c'] = $code;
		} elseif (newsletter_is_email_address($recipient)) {
			// recipient is an email address
			$params['e'] = $recipient;
			$params['c'] = $code;
		}
	}
	
	$result = elgg_generate_url('default:object:newsletter:unsubscribe', $params);
	
	return elgg_normalize_url($result);
}

/**
 * Generate a unsubscribe code to be used in validation
 *
 * @param ElggEntity $container Which newsletter container (ElggSite or ElggGroup)
 * @param string|int $recipient The user_guid or email address of the recipient
 *
 * @return bool|string The unsubscribe code or false on failure
 */
function newsletter_generate_unsubscribe_code(ElggEntity $container, $recipient) {
	
	if (!$container instanceof \ElggSite && !$container instanceof \ElggGroup) {
		return false;
	}
	
	if (empty($recipient)) {
		return false;
	}
	
	// make sure we have a user_guid or email address
	if (!is_numeric($recipient) && !newsletter_is_email_address($recipient)) {
		return false;
	}
	
	if (is_numeric($recipient)) {
		$recipient = (int) $recipient;
	}
	
	$plugin = elgg_get_plugin_from_id('newsletter');
	$hmac = elgg_build_hmac([
		$container->getGUID(),
		$recipient,
		$plugin->time_created,
	]);
	return $hmac->getToken();
}

/**
 * Validate a provided unsubscribe code
 *
 * @param ElggEntity $container Which newsletter container (ElggSite or ElggGroup)
 * @param string|int $recipient The user_guid or email address of the recipient
 * @param string     $code      The unsubscribe code the recipient provided
 *
 * @return bool	true is valid or false on failure
 */
function newsletter_validate_unsubscribe_code(ElggEntity $container, $recipient, $code) {
	
	if (!$container instanceof \ElggSite && !$container instanceof \ElggGroup) {
		return false;
	}
	
	if (empty($recipient)) {
		return false;
	}
	
	// make sure we have a user_guid or email address
	if (!is_numeric($recipient) && !newsletter_is_email_address($recipient)) {
		return false;
	}
	
	// generate the code as it should be
	$correct_code = newsletter_generate_unsubscribe_code($container, $recipient);
	
	// check for a match
	if ($code === $correct_code) {
		return true;
	}
	
	return false;
}

/**
 * Find a subscription entity for the given email address
 *
 * @param string $email The email address to find the subscription for
 *
 * @return bool|NewsletterSubscription The found subscription or false
 */
function newsletter_get_subscription($email) {
	
	if (!newsletter_is_email_address($email)) {
		return false;
	}
	
	// ignore access
	$entities = elgg_call(ELGG_IGNORE_ACCESS, function() use ($email) {
		return elgg_get_entities([
			'type' => 'object',
			'subtype' => NewsletterSubscription::SUBTYPE,
			'limit' => 1,
			'metadata_name_value_pairs' => [
				'name' => 'title',
				'value' => $email,
			],
		]);
	});
	
	if (!empty($entities)) {
		return $entities[0];
	}
	
	return false;
}

/**
 * Block an user from all newsletters
 *
 * @param ElggUser $user The user to block
 *
 * @return boolean True on success, false on failure
 */
function newsletter_unsubscribe_all_user(ElggUser $user) {
	
	$site = elgg_get_site_entity();
	
	// remove site subscription
	remove_entity_relationship($user->guid, NewsletterSubscription::SUBSCRIPTION, $site->guid);
	
	// remove all subscriptions
	$entities = elgg_get_entities([
		'type' => 'group',
		'limit' => false,
		'relationship' => NewsletterSubscription::SUBSCRIPTION,
		'relationship_guid' => $user->guid,
		'callback' => function($row) {
			return (int) $row->guid;
		},
	]);
	
	if (!empty($entities)) {
		foreach ($entities as $entity_guid) {
			remove_entity_relationship($user->guid, NewsletterSubscription::SUBSCRIPTION, $entity_guid);
		}
	}
	
	// add to general blacklist
	if (!check_entity_relationship($user->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid)) {
		$result = (bool) add_entity_relationship($user->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid);
	} else {
		$result = true;
	}
	
	// remove email subscriptions (if any)
	$subscription = newsletter_get_subscription($user->email);
	
	if (!empty($subscription)) {
		$subscription->delete();
	}
	
	return $result;
}

/**
 * Block an email address from all newsletters
 *
 * @param string $email The email address to block
 *
 * @return boolean True on success, false on failure
 */
function newsletter_unsubscribe_all_email($email) {
	
	if (!newsletter_is_email_address($email)) {
		return false;
	}
	
	// get subscription
	$subscription = newsletter_get_subscription($email);
	if (!empty($subscription)) {
		return false;
	}
	
	$subscription = new NewsletterSubscription();
	$subscription->title = $email;
	
	if (!$subscription->save()) {
		return false;
	}
	
	// remove all existing subscriptions
	remove_entity_relationships($subscription->guid, NewsletterSubscription::SUBSCRIPTION);
	
	// add to general blacklist
	$site = elgg_get_site_entity();
	
	if (check_entity_relationship($subscription->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid)) {
		// already blocked
		return true;
	}
	
	return (bool) add_entity_relationship($subscription->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid);
}

/**
 * Convert an email subscription on the newsletters to a user setting
 *
 * @param NewsletterSubscription $subscription The found email subscription
 * @param ElggUser               $user         The user to save the new settings to
 *
 * @return bool	true on success or false on failure
 */
function newsletter_convert_subscription_to_user_setting(NewsletterSubscription $subscription, ElggUser $user) {
	
	// check global block list
	$site = elgg_get_site_entity();
	if (check_entity_relationship($subscription->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid)) {
		// copy the block all
		add_entity_relationship($user->guid, NewsletterSubscription::GENERAL_BLACKLIST, $site->guid);
	} else {
		// check for subscriptions
		$subscriptions = $subscription->getEntitiesFromRelationship([
			'relationship' => NewsletterSubscription::SUBSCRIPTION,
			'limit' => false,
			'batch' => true,
		]);
		
		foreach ($subscriptions as $entity) {
			newsletter_subscribe_user($user, $entity);
		}
			
		// check for blocks
		$blocked = $subscription->getEntitiesFromRelationship([
			'relationship' => NewsletterSubscription::BLACKLIST,
			'limit' => false,
			'batch' =>true,
		]);
			
		foreach ($blocked as $entity) {
			newsletter_unsubscribe_user($user, $entity);
		}
	}
	
	// remove email subscription
	return (bool) $subscription->delete();
}

/**
 * Checks if group newsletter is allowed
 *
 * @param ElggGroup $group group entity
 *
 * @return bool
 */
function newsletter_is_group_enabled(ElggGroup $group = null) {
	static $plugin_setting;
	
	// make sure we only get the plugin setting once
	if (!isset($plugin_setting)) {
		$plugin_setting = elgg_get_plugin_setting('allow_groups', 'newsletter') === 'yes';
	}
	
	// check the setting of the group (if any)
	if (!$plugin_setting) {
		return false;
	}
	
	if (!$group instanceof ElggGroup) {
		// no group just checking plugin setting
		return true;
	}
	
	// check the setting of the group
	return $group->isToolEnabled('newsletter');
}

/**
 * Sends the preview newsletter
 *
 * @param Newsletter $entity newsletter to be send
 * @param string     $email  emailaddress of the recipient
 *
 * @return bool if sending is succes or not
 */
function newsletter_send_preview(Newsletter $entity, $email) {
	
	if (empty($email)) {
		return false;
	}
	
	$site = elgg_get_site_entity();
	$container = $entity->getContainerEntity();
	
	// build correct subject
	if ($entity->subject) {
		$message_subject = $entity->subject;
	} else {
		$message_subject = elgg_echo('newsletter:subject', [$container->getDisplayName(), $entity->getDisplayName()]);
	}
	//  plaintext message
	$message_plaintext_content = elgg_echo('newsletter:plain_message', [$entity->getURL()]);
	
	// html content
	$message_html_content = elgg_view_layout('newsletter', ['entity' => $entity]);
	
	// add unsubscribe link
	$unsubscribe_link = newsletter_generate_unsubscribe_link($container, $email);
	$message_html_content = str_ireplace(urlencode("{unsublink}"), $unsubscribe_link, $message_html_content);
	
	// replace online link
	$online_link = $entity->getURL();
	$new_online_link = $online_link . '?e=' . $email;
	
	$message_html_content = str_ireplace($online_link, $new_online_link, $message_html_content);
	
	if ((elgg_get_plugin_setting('custom_from', 'newsletter') === 'yes') && !empty($entity->from)) {
		// from is validated to a valid email address in the newsletter save action
		$from = new Address($entity->from, $container->getDisplayName());
	} else {
		// default to the container email address
		$from = new Address($site->getEmailAddress(), $container->getDisplayName());
	}
	
	// make email
	$email = Email::factory([
		'to' => $email,
		'from' => $from,
		'subject' => $message_subject,
		'body' => $message_plaintext_content,
		'params' => [
			'html_message' => $message_html_content,
		],
	]);
	
	// send preview
	return elgg_send_email($email);
}

/**
 * Returns all the available templates, these include those provided by themes
 * and the saved templates
 *
 * Other plugins/themes can provide their own template if the create a view
 * newsletter/templates/<some name>/{body|css}
 *
 * @param int         $container_guid The container of the current newsletter
 * @param \ElggEntity $entity         The current newsletter (optional) if editing
 *
 * @return array The available templates
 */
function newsletter_get_available_templates($container_guid, $entity = null) {
	$result = [];
	
	// detect templates provided by themes/plugins
	$locations = _elgg_services()->views->listViews();
	$pattern = '/^newsletter\/templates\/(?P<name>\w+)\/(body|css)$/';
	
	foreach ($locations as $view) {
		$matches = [];
		$res = preg_match($pattern, $view, $matches);
		
		if (empty($res)) {
			continue;
		}
		
		$name = elgg_extract('name', $matches);
		$lan_key = "newsletter:edit:template:select:{$name}";
		if (elgg_language_key_exists($lan_key)) {
			$title = elgg_echo($lan_key);
		} else {
			$title = $name;
		}
		
		$result[$name] = $title;
	}
	
	// get saved templates
	if (!empty($container_guid)) {
		$templates = elgg_get_entities([
			'type' => 'object',
			'subtype' => NewsletterTemplate::SUBTYPE,
			'container_guid' => $container_guid,
			'limit' => false,
		]);
		if (!empty($templates)) {
			foreach ($templates as $template) {
				$result[$template->guid] = $template->getDisplayName();
			}
		}
	}
	
	// the custom selection option
	unset($result['custom']); // make sure custom is last in the list (shouldn't be provided by a plugin/theme)
	$result['custom'] = elgg_echo('newsletter:edit:template:select:custom');
		
	return elgg_trigger_plugin_hook('templates', 'newsletter', [
		'container_guid' => $container_guid,
		'entity' => $entity,
	], $result);
}

/**
 * Process an uploaded CSV file to find new recipients.
 *
 * @param array $recipients previous recipients, to prevent duplicates
 * Contains:
 *
 * user_guids => array() existing users
 * emails => array() extra email addresses
 *
 * @return array
 */
function newsletter_process_csv_upload(array $recipients) {
	
	// is a file uploaded
	$csv = elgg_get_uploaded_file('csv');
	if (!$csv instanceof UploadedFile) {
		return $recipients;
	}
	
	// this could take a while (for big csv files)
	set_time_limit(0);
	
	// open the file as CSV
	$fh = $csv->openFile('r');
	
	$email_column = false;
	
	// try to find an email column (in the first 2 rows)
	for ($i = 0; $i < 2; $i++) {
		$fh->seek($i);
		
		$row = $fh->fgetcsv(';', '\'');
		if (empty($row)) {
			continue;
		}
		
		foreach ($row as $index => $field) {
			if (newsletter_is_email_address($field)) {
				$email_column = $index;
				break;
			}
		}
	}
	
	// found an email column
	if ($email_column === false) {
		// no email column found, report this
		system_message(elgg_echo('newsletter:csv:no_email'));
		
		return $recipients;
	}
	
	$counter = 0;
	
	// start at the beginning
	$fh->rewind();
	
	while (!$fh->eof()) {
		$row = $fh->fgetcsv(';');
		
		// get the email address
		$email = elgg_extract($email_column, $row);
		
		// make sure it's a valid email address
		if (!newsletter_is_email_address($email)) {
			continue;
		}
		
		$counter++;
		
		// is this email address already in the recipients list
		if (in_array($email, $recipients['emails'])) {
			continue;
		}
		
		// check for an existing user
		$users = elgg_call(ELGG_IGNORE_ACCESS, function () use ($email) {
			return get_user_by_email($email);
		});
		if (!empty($users)) {
			foreach ($users as $user) {
				if (in_array($user->guid, $recipients['user_guids'])) {
					continue(2);
				}
			}
		}
		
		// email address wasn't added yet
		// so add to the list
		$users = elgg_call(ELGG_IGNORE_ACCESS, function () use ($email) {
			return get_user_by_email($email);
		});
		if (!empty($users)) {
			$recipients['user_guids'][] = $users[0]->guid;
		} else {
			$recipients['emails'][] = $email;
		}
	}
	
	// done, report the added emails
	system_message(elgg_echo('newsletter:csv:added', [$counter]));
	
	return $recipients;
}

/**
 * Get the plugin settings for URL postfix
 *
 * @return bool|array
 */
function newsletter_get_url_postfix() {
	static $result;
	
	if (isset($result)) {
		return $result;
	}
	
	$result = false;
	
	$url_postfix = elgg_get_plugin_setting('url_postfix', 'newsletter');
	
	if (!is_null($url_postfix) && ($url_postfix !== false)) {
		$lines = explode(PHP_EOL, $url_postfix);
		$temp_result = [];
		
		foreach ($lines as $line) {
			list($name, $value) = explode('=', $line);
			
			if (is_null($name) || ($name === false)) {
				continue;
			}
			if (is_null($value) || ($value === false)) {
				continue;
			}
			
			$temp_result[$name] = $value;
		}
		
		if (!empty($temp_result)) {
			$result = $temp_result;
		}
	}
	return $result;
}

/**
 * Add the URL postfix to all url's in the newsletter content
 *
 * @param string     $html_content the content of the newletter
 * @param Newsletter $newsletter   the source newsletter to use for some replacements in the postfix
 *
 * @return string
 */
function newsletter_apply_url_postfix($html_content, Newsletter $newsletter) {
	static $pattern;
	
	if (!($newsletter instanceof Newsletter)) {
		return $html_content;
	}
	
	// get the postfix settings
	$url_postfix_settings = newsletter_get_url_postfix();
	if (empty($url_postfix_settings)) {
		return $html_content;
	}
	
	// build the pattern once
	if (!isset($pattern)) {
		// convert site url to preg friendly version
		$preg_site_url = elgg_get_site_url();
		$preg_site_url = substr($preg_site_url, 0, -1);
		$preg_site_url = str_replace("/", "\\/", $preg_site_url);
		$preg_site_url = str_replace(".", "\\.", $preg_site_url);
		
		$pattern = '/\shref=([\'"]' . $preg_site_url . '[^\'"]*[\'"])/i';
	}
	
	// find all matches
	$matches = [];
	preg_match_all($pattern, $html_content, $matches);
	
	if (empty($matches) || !isset($matches[1])) {
		return $html_content;
	}
	
	// url postfix placeholder replacements
	$replacements = ['{guid}' => $newsletter->getGUID()];
	
	foreach ($url_postfix_settings as $name => $value) {
		$url_postfix_settings[$name] = str_ireplace(array_keys($replacements), array_values($replacements), $value);
	}
	
	// go through all the matches
	$urls = $matches[1];
	$urls = array_unique($urls);
	
	foreach ($urls as $url) {
		// remove wrapping quotes from the url
		$real_url = substr($url, 1, -1);
		// add the postfix params
		$new_url = elgg_http_add_url_query_elements($real_url, $url_postfix_settings);
		// make the correct replacement string
		$replacement = str_replace($real_url, $new_url, $url);
		
		// replace the url in the content
		$html_content = str_replace($url, $replacement, $html_content);
	}
	
	return $html_content;
}

/**
 * Check if embed functionality is availble for Newslettes
 *
 * Eg require blog or static
 *
 * @return bool
 */
function newsletter_embed_available() {
	
	if (elgg_is_active_plugin('blog')) {
		return true;
	} elseif (elgg_is_active_plugin('static')) {
		return true;
	}
	
	return false;
}

/**
 * Display conten in the correct layout for embedding in Newsletter
 *
 * @param ElggEntity $entity the entity to embed
 * @param array      $vars   optional variables to pass to the embed view
 *
 * @return bool|string
 */
function newsletter_view_embed_content(ElggEntity $entity, array $vars = []) {
	
	$vars['entity'] = $entity;
	
	$type = $entity->getType();
	$subtype = $entity->getSubtype();
	
	if (elgg_view_exists("newsletter/embed/{$type}/{$subtype}")) {
		return elgg_view("newsletter/embed/{$type}/{$subtype}", $vars);
	} elseif (elgg_view_exists("newsletter/embed/{$type}/default")) {
		return elgg_view("newsletter/embed/{$type}/default", $vars);
	} elseif (elgg_view_exists("newsletter/embed/default")) {
		return elgg_view("newsletter/embed/default", $vars);
	}
	
	return false;
}

/**
 * Validate the custom from email address
 *
 * This also triggers a plugin hook 'from_email', 'newsletter' for other plugins to hook into
 * Supplied params:
 * - email: the email address to validate
 *
 * @param string $from_email the email address to check
 *
 * @return bool
 */
function newsletter_validate_custom_from($from_email) {
	
	if (empty($from_email)) {
		// empty is allowed, sending will fallback to container
		return true;
	}
	
	if (!newsletter_is_email_address($from_email)) {
		// not an email address, always fail
		return false;
	}
	
	$result = true;
	
	// check plugin settings domain limitations
	$plugin_setting = elgg_get_plugin_setting('custom_from_domains', 'newsletter');
	if (!empty($plugin_setting)) {
		$result = false;
		$plugin_setting = string_to_tag_array($plugin_setting);
		
		list(, $domain) = explode('@', $from_email);
		foreach ($plugin_setting as $allowed_domain) {
			if ($domain === $allowed_domain) {
				// custom from is from an allowed domain
				$result = true;
				break;
			}
		}
	}
	
	// trigger a plugin hook so others are allowed to validate
	return (bool) elgg_trigger_plugin_hook('from_email', 'newsletter', ['email' => $from_email], $result);
}

/**
 * Register newsletter subscription items to the title menu
 *
 * @param ElggEntity $container_entity for which container entity to check subscriptions
 *
 * @return void
 */
function newsletter_register_title_menu_items(ElggEntity $container_entity) {
	
	if (!$container_entity instanceof ElggSite && !$container_entity instanceof ElggGroup) {
		return;
	}
	
	if ($container_entity instanceof ElggGroup && !$container_entity->isToolEnabled('newsletter')) {
		return;
	}
	
	$user = elgg_get_logged_in_user_entity();
	if ($user instanceof ElggUser) {
		$subscribed = newsletter_check_user_subscription($user, $container_entity);
		
		elgg_register_menu_item('title', [
			'name' => 'newsletter_subscribe',
			'icon' => 'envelope-open-text',
			'text' => elgg_echo("newsletter:subscribe:{$container_entity->type}"),
			'title' => elgg_echo('newsletter:subscribe:user:description:subscribe', [$container_entity->getDisplayName()]),
			'href' => elgg_generate_action_url('newsletter/subscribe', [
				'guid' => $container_entity->guid,
				'user_guid' => $user->guid,
			]),
			'link_class' => [
				'elgg-button',
				'elgg-button-action',
			],
			'item_class' => $subscribed ? 'hidden' : null,
			'data-toggle' => 'newsletter_unsubscribe',
		]);
		elgg_register_menu_item('title', [
			'name' => 'newsletter_unsubscribe',
			'icon' => 'envelope',
			'text' => elgg_echo("newsletter:unsubscribe:{$container_entity->type}"),
			'title' => elgg_echo('newsletter:subscribe:user:description:unsubscribe', [$container_entity->getDisplayName()]),
			'href' => elgg_generate_action_url('newsletter/subscribe', [
				'guid' => $container_entity->guid,
				'user_guid' => $user->guid,
			]),
			'link_class' => [
				'elgg-button',
				'elgg-button-action',
			],
			'item_class' => $subscribed ? null : 'hidden',
			'data-toggle' => 'newsletter_subscribe',
		]);
	} else {
		// logged out user
		elgg_register_menu_item('title', [
			'name' => 'newsletter_subscribe',
			'icon' => 'envelope-open-text',
			'text' => elgg_echo("newsletter:subscribe:{$container_entity->type}"),
			'title' => elgg_echo('newsletter:subscribe:user:description:subscribe', [$container_entity->getDisplayName()]),
			'href' => elgg_http_add_url_query_elements('ajax/form/newsletter/subscribe', [
				'guid' => $container_entity->guid,
			]),
			'link_class' => [
				'elgg-button',
				'elgg-button-action',
				'elgg-lightbox',
			],
		]);
	}
}
