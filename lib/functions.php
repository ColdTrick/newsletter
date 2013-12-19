<?php

	/**
	 * Start the commandline to send a newsletter
	 * This is offloaded because it could take a while and/or resources
	 *
	 * @param Newsletter $entity
	 */
	function newsletter_start_commandline_sending(Newsletter $entity) {
		
		if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			// prepare commandline settings
			$settings = array(
				"entity_guid" => $entity->getGUID(),
				"host" => $_SERVER["HTTP_HOST"],
				"memory_limit" => ini_get("memory_limit"),
				"secret" => newsletter_generate_commanline_secret($entity->getGUID())
			);
			if(isset($_SERVER["HTTPS"])){
				$settings["https"] = $_SERVER["HTTPS"];
			}
			
			// which script to run
			$script_location = dirname(dirname(__FILE__)) . "/procedures/cli.php";
			
			// convert settings to commandline params
			$query_string = http_build_query($settings, "", " ");
			
			// start the correct commandline
			if (PHP_OS === "WINNT") {
				pclose(popen("start /B php " . $script_location . " " . $query_string, "r"));
			} else {
				exec("php " . $script_location . " " . $query_string . " > /dev/null &");
			}
		}
	}
	
	/**
	 * Generate a secret to be used for validation
	 *
	 * @param int $entity_guid
	 * @return boolean|string
	 */
	function newsletter_generate_commanline_secret($entity_guid) {
		$result = false;
		
		$entity_guid = sanitise_int($entity_guid, false);
		
		if (!empty($entity_guid)) {
			$plugin = elgg_get_plugin_from_id("newsletter");
			
			$result = hash_hmac("sha256", ($plugin->getGUID() . "|" . $entity_guid . "|" . $plugin->time_created), get_site_secret());
		}
		
		return $result;
	}
	
	/**
	 * Validate if the provided secret is correct
	 *
	 * @param int $entity_guid
	 * @param string $secret
	 * @return boolean
	 */
	function newsletter_validate_commandline_secret($entity_guid, $secret) {
		$result = false;
		
		$entity_guid = sanitise_int($entity_guid, false);
		
		if (!empty($entity_guid) && !empty($secret)) {
			$correct_secret = newsletter_generate_commanline_secret($entity_guid);
			
			if (!empty($correct_secret) && ($correct_secret === $secret)) {
				$result = true;
			}
		}
		
		return $result;
	}
	
	function newsletter_process($entity_guid) {
		
		$entity_guid = sanitise_int($entity_guid, false);
		
		if (!empty($entity_guid)) {
			// ignore access
			$ia = elgg_set_ignore_access(true);
			
			$entity = get_entity($entity_guid);
			// is this a Newsletter
			if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
				$logging = array(
					"start_time" => time()
				);
				
				$site = elgg_get_site_entity();
				$container = $entity->getContainerEntity();
				$dbprefix = elgg_get_config("dbprefix");
				
				// ================================
				// set newsletter status to sending
				// ================================
				$entity->status = "sending";
				$entity->start_time = $logging["start_time"];
				
				// ==================
				// get the recipients
				// ==================
				// basic set of user selection options
				$basic_user_options = array(
					"type" => "user",
					"limit" => false,
					"selects" => array("ue.email"),
					"joins" => array("JOIN " . $dbprefix . "users_entity ue ON e.guid = ue.guid"),
					"wheres" => array(
						"(e.guid NOT IN (SELECT guid_one
							FROM " . $dbprefix . "entity_relationships
							WHERE relationship = '" . NewsletterSubscription::GENERAL_BLACKLIST . "'
							AND guid_two = " . $site->getGUID() . ")
						)",
						"(e.guid NOT IN (SELECT guid_one
							FROM " . $dbprefix . "entity_relationships
							WHERE relationship = '" . NewsletterSubscription::BLACKLIST . "'
							AND guid_two = " . $container->getGUID() . ")
						)"
					),
					"callback" => "newsletter_user_row_to_subscriber_info"
				);
				
				$filtered_recipients = array(
					"users" => array(),
					"emails" => array()
				);
				$recipients = $entity->recipients;
				if (!empty($recipients)) {
					$recipients = json_decode($recipients, true);
				} else {
					// no recipients so report error
					$entity->status = "sent";
					
					return false;
				}
				
				// recipients is an array consisting of:
				// - user_guids: 	individual users
				// - group_guids:	groups to send the content to
				// - emails:		individual email addresses
				// - subscribers:	(int) whether or not to add the subscribers of the container
				// - members:		(int) whether or not to aad the member of the container
				$user_guids = elgg_extract("user_guids", $recipients);
				if (!empty($user_guids)) {
					if (!is_array($user_guids)) {
						$user_guids = array($user_guids);
					}
					
					// convert to a format we can use
					$options = $basic_user_options;
					$options["wheres"][] = "(e.guid IN (" . implode(",", $user_guids) . "))";
					
					$users = elgg_get_entities($options);
					if (!empty($users)) {
						$new_users = array();
						foreach ($users as $user) {
							$new_users[$user["guid"]] = $user["email"];
						}
						$filtered_recipients["users"] += $new_users;
					}
				}
				
				$group_guids = elgg_extract("group_guids", $recipients);
				if (!empty($group_guids)) {
					if (!is_array($group_guids)) {
						$group_guids = array($group_guids);
					}
					
					$options = $basic_user_options;
					$options["joins"][] = "JOIN " . $dbprefix . "entity_relationships r ON e.guid = r.guid_one";
					$options["wheres"][] = "(r.guid_two IN (" . implode(",", $group_guids) . ") AND r.relationship = 'member')";
					
					$users = elgg_get_entities($options);
					if (!empty($users)) {
						$new_users = array();
						foreach ($users as $user) {
							$new_users[$user["guid"]] = $user["email"];
						}
						$filtered_recipients["users"] += $new_users;
					}
				}
				
				$subscribers = elgg_extract("subscribers", $recipients);
				if (!empty($subscribers)) {
					$subscribers = newsletter_get_subscribers($container);
					
					$filtered_recipients["users"] += $subscribers["users"];
					$filtered_recipients["emails"] = array_merge($filtered_recipients["emails"], $subscribers["emails"]);
				}
				
				$members = elgg_extract("members", $recipients);
				if (!empty($members)) {
					$relationship = "member";
					if (elgg_instanceof($container, "site")) {
						$relationship = "member_of_site";
					}
					
					$options = $basic_user_options;
					$options["relationship"] = $relationship;
					$options["relationship_guid"] = $container->getGUID();
					$options["inverse_relationship"] = true;
					
					$users = elgg_get_entities_from_relationship($options);
					if (!empty($users)) {
						$new_users = array();
						foreach ($users as $user) {
							$new_users[$user["guid"]] = $user["email"];
						}
						$filtered_recipients["users"] += $new_users;
					}
				}
				
				$emails = elgg_extract("emails", $recipients);
				if (!empty($emails)) {
					if (!is_array($emails)) {
						$emails = array($emails);
					}
					
					// get blocked users
					$options = array(
						"type" => "user",
						"limit" => false,
						"selects" => array("ue.email"),
						"joins" => array("JOIN " . $dbprefix . "users_entity ue ON e.guid = ue.guid"),
						"wheres" => array(
							"(ue.email IN ('" . implode("','", $emails) . "'))",
							"(e.guid IN (SELECT guid_one
								FROM " . $dbprefix . "entity_relationships
								WHERE relationship = '" . NewsletterSubscription::GENERAL_BLACKLIST . "'
								AND guid_two = " . $site->getGUID() . ")
							OR
							e.guid IN (SELECT guid_one
								FROM " . $dbprefix . "entity_relationships
								WHERE relationship = '" . NewsletterSubscription::BLACKLIST . "'
								AND guid_two = " . $container->getGUID() . ")
							)"
						),
						"callback" => "newsletter_user_row_to_subscriber_info"
					);
					
					$users = elgg_get_entities($options);
					if (!empty($users)) {
						$blocked_emails = array();
						foreach ($users as $user) {
							$blocked_emails[] = $user["email"];
						}
						
						$emails = array_diff($email, $blocked_emails);
					}
					
					if (!empty($emails)) {
						// get blocked emails
						$options = array(
							"type" => "object",
							"subtype" => NewsletterSubscription::SUBTYPE,
							"limit" => false,
							"selects" => array("oe.title AS email"),
							"joins" => array("JOIN " . $dbprefix . "objects_entity oe ON e.guid = oe.guid"),
							"wheres" => array(
								"(oe.title IN ('" . implode("','", $emails) . "'))",
								"(e.guid IN (SELECT guid_one
									FROM " . $dbprefix . "entity_relationships
									WHERE relationship = '" . NewsletterSubscription::GENERAL_BLACKLIST . "'
									AND guid_two = " . $site->getGUID() . ")
								OR
								e.guid IN (SELECT guid_one
									FROM " . $dbprefix . "entity_relationships
									WHERE relationship = '" . NewsletterSubscription::BLACKLIST . "'
									AND guid_two = " . $container->getGUID() . ")
								)"
							),
							"callback" => "newsletter_user_row_to_subscriber_info"
						);
						
						$subscriptions = elgg_get_entities($options);
						if (!empty($subscriptions)) {
							$blocked_emails = array();
							foreach ($subscriptions as $subscription) {
								$blocked_emails[] = $subscription["email"];
							}
							
							$emails = array_diff($email, $blocked_emails);
						}
						
						if (!empty($emails)) {
							$filtered_recipients["emails"] = array_merge($filtered_recipients["emails"], $emails);
						}
					}
				}
				
				// ======================
				// get newsletter content
				// ======================
				if ($entity->subject) {
					$message_subject = $entity->subject;
				} else {
					$message_subject = elgg_echo("newsletter:subject", array($container->name, $entity->title));
				}
				$message_plaintext_content = elgg_echo("newsletter:plain_message", array($entity->getOnlineURL()));
				
				$message_html_content = elgg_view_layout("newsletter", array("entity" => $entity));
				// convert to inline CSS for email clients
				$message_html_content = html_email_handler_css_inliner($message_html_content);
				
				// =======================
				// proccess all recipients
				// =======================
				$send_options = array(
					"from" => html_email_handler_make_rfc822_address($container),
					"subject" => $message_subject,
					"plaintext_message" => $message_plaintext_content
				);
				
				foreach ($filtered_recipients as $type => $recipients) {
					
					if (!empty($recipients)) {
						
						foreach ($recipients as $id => $recipient) {
							$recipient_log = array(
								"type" => $type,
								"email" => $recipient,
								"time" => date(DATE_RFC1123),
								"timestamp" => time(),
								"status" => false
							);
							
							// =============================================
							// create individual footer for unsubscribe link
							// =============================================
							if ($type == "users") {
								$recipient_log["guid"] = $id;
								
								$unsubscribe_link = newsletter_generate_unsubscribe_link($container, $id);
							} else {
								$unsubscribe_link = newsletter_generate_unsubscribe_link($container, $recipient);
							}
							
							// place the unsubscribe link in the message
							$message_html_content_user = str_ireplace("{{{unsubscribe_link}}}", $unsubscribe_link, $message_html_content);
							
							// =========
							// send mail
							// =========
							$send_options["to"] = $recipient;
							$send_options["html_message"] = $message_html_content_user;
							
							$recipient_log["status"] = html_email_handler_send_email($send_options);
							
							if (true && !empty($recipient_log["guid"])) {
								$entity->addRelationship($recipient_log["guid"], Newsletter::SEND_TO);
							}
							
							// ==============
							// add to logging
							// ==============
							$logging["recipients"][] = $recipient_log;
							
							$entity->saveLogging($logging);
						}
					}
				}
				
				$logging["end_time"] = time();
				
				$entity->saveLogging($logging);
				
				// =============================
				// set newsletter status to done
				// =============================
				$entity->status = "sent";
				
				// ========================
				// send status notification
				// ========================
				if (!empty($entity->status_notification) && newsletter_is_email_address($entity->status_notification)) {
					$from = html_email_handler_make_rfc822_address($site);
					$subject = elgg_echo("newsletter:status_notification:subject");
					$message = elgg_echo("newsletter:status_notification:message", array($entity->title, $entity->getURL()));
					
					elgg_send_email($from, $entity->status_notification, $subject, $message);
				}
			}
			
			// restore access
			elgg_set_ignore_access($ia);
		}
	}
	
	/**
	 * Format a recipient to be listed in the recipient autocomplete or listing
	 *
	 * @param 	mixed	$recipient	Can be an ElggUser, ElggGroup or email address
	 * @return 	array	Contains, of false on failure
	 * 						- type : user, group or email
	 * 						- label: used in the autocomplete dropdown
	 * 						- content: used in the listing view
	 * 						- value: eighter a guid of email address
	 */
	function newsletter_format_recipient($recipient) {
		$result = false;
		
		if (!empty($recipient)) {
			if (elgg_instanceof($recipient, "user")) {
				$icon = elgg_view_entity_icon($recipient, "tiny", array(
					"use_hover" => false,
					"use_link" => false
				));
				
				// label for autoselect listing
				$label = elgg_view_image_block($icon, $recipient->name, array("class" => "elgg-autocomplete-item"));
				
				// content for if selected
				$content = "<div class='newsletter-recipient-result phs'>";
				$content .= elgg_view("input/hidden", array("name" => "user_guids[]", "value" => $recipient->getGUID()));
				$content .= elgg_view_image_block($icon, $recipient->name, array("image_alt" => elgg_view_icon("delete-alt")));
				$content .= "</div>";
				
				$result = array(
					"type" => "user",
					"value" => $recipient->getGUID(),
					"label" => $label,
					"content" => $content
				);
			} elseif (elgg_instanceof($recipient, "group")) {
				$icon = elgg_view_entity_icon($recipient, "tiny", array(
					"use_hover" => false,
					"href" => false
				));
				
				// label for autoselect listing
				$name = "<span>" . elgg_echo("groups:group") . ": </span>" . $recipient->name;
				$name .= "<span class='elgg-quiet'> (" . $recipient->getMembers(0, 0, true) . " " . elgg_echo("groups:member") . ")</span>";
				
				$label = elgg_view_image_block($icon, $name, array("class" => "elgg-autocomplete-item newsletter-recipient-autocomplete-group"));
				
				// content for if selected
				$name = $recipient->name;
				$name .= "<span class='elgg-quiet'> (" . $recipient->getMembers(0, 0, true) . " " . elgg_echo("groups:member") . ")</span>";
				
				$content = "<div class='newsletter-recipient-result phs'>";
				$content .= elgg_view("input/hidden", array("name" => "group_guids[]", "value" => $recipient->getGUID()));
				$content .= elgg_view_image_block($icon, $name, array("image_alt" => elgg_view_icon("delete-alt")));
				$content .= "</div>";
				
				$result = array(
					"type" => "group",
					"value" => $recipient->getGUID(),
					"label" => $label,
					"content" => $content
				);
			} elseif (newsletter_is_email_address($recipient)) {
				$name = "<span>" . elgg_echo("newsletter:recipients:email") . ": </span>" . $recipient;
				$label = elgg_view_image_block("", $name, array("class" => "elgg-autocomplete-item newsletter-recipient-autocomplete-email"));
					
				$content = "<div class='newsletter-recipient-result phs'>";
				$content .= elgg_view("input/hidden", array("name" => "emails[]", "value" => $recipient));
				$content .= elgg_view_image_block("", $recipient, array("image_alt" => elgg_view_icon("delete-alt")));
				$content .= "</div>";
					
				$result = array(
					"type" => "email",
					"label" => $label,
					"content" => $content
				);
			}
		}
		
		return $result;
	}
	
	/**
	 * Get all the people that subscribed to the newsletter of this container
	 *
	 * @param 	ElggEntity 	$container	Which container
	 * @param 	bool		$count		Return just a count, not the actual subscribers
	 * @return 	array | int				On success or false on failure
	 */
	function newsletter_get_subscribers(ElggEntity $container, $count = false) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group"))) {
			// get the subscribers
			if (!$count) {
				$result = array(
					"users" => array(),
					"emails" => array()
				);
				
				// get all subscribed community members
				$options = array(
					"type" => "user",
					"selects" => array("ue.email"),
					"site_guids" => false,
					"limit" => false,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true,
					"joins" => array("JOIN " . elgg_get_config("dbprefix") . "users_entity ue ON e.guid = ue.guid"),
					"callback" => "newsletter_user_row_to_subscriber_info"
				);
				
				// @todo make this easier????
				$tmp_users = elgg_get_entities_from_relationship($options);
				if (!empty($tmp_users)) {
					foreach ($tmp_users as $tmp_user) {
						$result["users"][$tmp_user["guid"]] = $tmp_user["email"];
					}
				}
				
				// check the email subscriptions
				$options = array(
					"type" => "object",
					"subtype" => NewsletterSubscription::SUBTYPE,
					"selects" => array("oe.title"),
					"limit" => false,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true,
					"joins" => array("JOIN " . elgg_get_config("dbprefix") . "objects_entity oe ON e.guid = oe.guid"),
					"callback" => "newsletter_subscription_row_to_subscriber_info"
				);
				
				$result["emails"] = elgg_get_entities_from_relationship($options);
			} else {
				// get all subscribed community members
				$options = array(
					"type" => "user",
					"site_guids" => false,
					"count" => true,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true
				);
				
				$result = elgg_get_entities_from_relationship($options);
				
				// check the email subscriptions
				$options = array(
					"type" => "object",
					"subtype" => NewsletterSubscription::SUBTYPE,
					"count" => true,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true,
				);
				
				$result += elgg_get_entities_from_relationship($options);
			}
		}
		
		return $result;
	}
	
	/**
	 * Check if a user is subscribed to a container entity
	 *
	 * @param 	ElggUser 	$user	The user to check
	 * @param 	ElggEntity 	$entity	The container entity to check against
	 * @return 	boolean				True => the user has a subscription, false => no subscription or error
	 */
	function newsletter_check_user_subscription(ElggUser $user, ElggEntity $entity) {
		$result = false;
		
		if (!empty($user) && !empty($entity)) {
			if (elgg_instanceof($user, "user") && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				$result = (bool) check_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID());
			}
		}
		
		return $result;
	}
	
	/**
	 * Add a subscription for an user to a container
	 *
	 * @param 	ElggUser 	$user	The user to subscribe
	 * @param 	ElggEntity 	$entity	The container entity to subscribe to
	 * @return 	boolean				true on success else false
	 */
	function newsletter_subscribe_user(ElggUser $user, ElggEntity $entity) {
		$result = false;
		
		if (!empty($user) && !empty($entity)) {
			if (elgg_instanceof($user, "user") && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// check if subscribed
				if (!check_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID())) {
					// not yet, so add
					$result = add_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID());
				} else {
					$result = true;
				}
				
				// remove blocklist relation
				remove_entity_relationship($user->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID());
				
				// remove general blocklist
				$site = elgg_get_site_entity();
				remove_entity_relationship($user->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
				
				// check if on email blacklist
				$subscription = newsletter_get_subscription($user->email);
				
				if (!empty($subscription)) {
					$subscription->removeRelationship($entity, NewsletterSubscription::BLACKLIST);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Add a subscription for an email address to a container
	 *
	 * @param 	string 		$email	The email address to add to the subscriptions
	 * @param 	ElggEntity 	$entity	The container entity to subscribe to
	 * @return 	boolean				true on success else false
	 */
	function newsletter_subscribe_email($email, ElggEntity $entity) {
		$result = false;
		
		if (!empty($email) && !empty($entity)) {
			if (newsletter_is_email_address($email) && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// check if email belongs to existing user
				$users = get_user_by_email($email);
				
				if (!empty($users)) {
					$result = newsletter_subscribe_user($users[0], $entity);
				} else {
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
					$result = (bool) $subscription->addRelationship($entity->getGUID(), NewsletterSubscription::SUBSCRIPTION);
					
					// remove blocklist relation
					remove_entity_relationship($subscription->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID());
					
					// remove general blocklist
					$site = elgg_get_site_entity();
					remove_entity_relationship($user->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Remove a subscription for an user to a container
	 *
	 * @param 	ElggUser 	$user	The user to unsubscribe
	 * @param 	ElggEntity 	$entity	The container entity to unsubscribe from
	 * @return 	boolean				true on success else false
	 */
	function newsletter_unsubscribe_user(ElggUser $user, ElggEntity $entity) {
		$result = false;
		
		if (!empty($user) && !empty($entity)) {
			if (elgg_instanceof($user, "user") && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// remove subscription
				remove_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID());
				
				// check if on email subscriptionlist
				$subscription = newsletter_get_subscription($user->email);
				
				if (!empty($subscription)) {
					$subscription->removeRelationship($entity->getGUID(), NewsletterSubscription::SUBSCRIPTION);
				}
		
				// check if blocked
				if (!check_entity_relationship($user->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID())) {
					// not yet, so add
					$result = add_entity_relationship($user->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID());
				} else {
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Remove a subscription for an email address to a container
	 *
	 * @param 	string		$email	The email address to remove from the subscriptions
	 * @param 	ElggEntity 	$entity	The container entity to unsubscribe from
	 * @return 	boolean				true on success else false
	 */
	function newsletter_unsubscribe_email($email, ElggEntity $entity) {
		$result = false;
		
		if (!empty($email) && !empty($entity)) {
			if (newsletter_is_email_address($email) && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// check if not existing user
				$users = get_user_by_email($email);
				
				if (!empty($users)) {
					// existing user
					$result = newsletter_unsubscribe_user($users[0], $entity);
				} else {
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
					$result = $subscription->addRelationship($entity->getGUID(), NewsletterSubscription::BLACKLIST);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Custom callback for elgg_get_* function to return a subset of information about an user
	 *
	 * @param stdObj	$row	A database row
	 * @return	array			contains [guid] => email
	 *
	 * @see elgg_get_entities()
	 */
	function newsletter_user_row_to_subscriber_info($row) {
		return array(
			"guid" => (int) $row->guid,
			"email" => $row->email
		);
	}
	
	/**
	 * Custom callback for elgg_get_* function to return the email address of a subscriber
	 *
	 * @param 	stdObj	$row	A database row
	 * @return	string			the email address of the subscriber
	 *
	 * @see elgg_get_entities()
	 */
	function newsletter_subscription_row_to_subscriber_info($row) {
		return $row->title;
	}
	
	/**
	 * Custom callback for elgg_get_* function to return the GUID of an entity
	 *
	 * @param 	stdObj	$row	A database row
	 * @return	int				The GUID of the entity
	 *
	 * @see elgg_get_entities()
	 */
	function newsletter_row_to_guid($row) {
		return (int) $row->guid;
	}
	
	/**
	 * A different interpretation of is_email_address()
	 * because PHP doesn't always correctly verify email addresses
	 *
	 * @param 	string 	$address	The email address to check
	 * @return 	bool				true if email, false otherwise
	 *
	 * @see is_email_address()
	 * @see filter_var()
	 */
	function newsletter_is_email_address($address) {
		$result = false;
		
		if (!empty($address)) {
			$regexpr = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
			
			$result = (bool) preg_match($regexpr, $address);
		}
		
		return $result;
	}
	
	/**
	 * Generate an URL so the recipient can directly unsubscribe from a newsletter
	 *
	 * @param 	ElggEntity 	$container	Which newsletter container (ElggSite or ElggGroup)
	 * @param 	string|int 	$recipient	The user_guid or email address of the recipient
	 * @return 	bool|string				The unsubscribe link or false on failure
	 */
	function newsletter_generate_unsubscribe_link(ElggEntity $container, $recipient) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group")) && !empty($recipient)) {
			$code = newsletter_generate_unsubscribe_code($container, $recipient);
			
			if (is_numeric($recipient)) {
				// recipient is an user_guid
				$result = "newsletter/unsubscribe/" . $container->getGUID() . "?u=" . $recipient . "&c=" . $code;
			} elseif (newsletter_is_email_address($recipient)) {
				// recipient is an email address
				$result = "newsletter/unsubscribe/" . $container->getGUID() . "?e=" . $recipient . "&c=" . $code;
			}
		}
		
		return $result;
	}
	
	/**
	 * Generate a unsubscribe code to be used in validation
	 *
	 * @param 	ElggEntity 	$container	Which newsletter container (ElggSite or ElggGroup)
	 * @param 	string|int	$recipient	The user_guid or email address of the recipient
	 * @return 	bool|string				The unsubscribe code or false on failure
	 */
	function newsletter_generate_unsubscribe_code(ElggEntity $container, $recipient) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group")) && !empty($recipient)) {
			// make sure we have a user_guid or email address
			if (is_numeric($recipient) || newsletter_is_email_address($recipient)) {
				$plugin = elgg_get_plugin_from_id("newsletter");
				
				$result = hash_hmac("sha256", ($container->getGUID() . "|" . $recipient . "|" . $plugin->time_created), get_site_secret());
			}
		}
		
		return $result;
	}
	
	/**
	 * Validate a provided unsubscribe code
	 *
	 * @param 	ElggEntity 	$container	Which newsletter container (ElggSite or ElggGroup)
	 * @param 	string|int	$recipient	The user_guid or email address of the recipient
	 * @param	string		$code		The unsubscribe code the recipient provided
	 * @return 	bool					true is valid or false on failure
	 */
	function newsletter_validate_unsubscribe_code(ElggEntity $container, $recipient, $code) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group")) && !empty($recipient)) {
			// make sure we have a user_guid or email address
			if (is_numeric($recipient) || newsletter_is_email_address($recipient)) {
				// generate the code as it should be
				$correct_code = newsletter_generate_unsubscribe_code($container, $recipient);
				
				// check for a match
				if ($code === $correct_code) {
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Find a subscription entity for the given email address
	 *
	 * @param 	string	$email	The email address to find the subscription for
	 * @return 	bool|NewsletterSubscription	The found subscription or false
	 */
	function newsletter_get_subscription($email) {
		$result = false;
		
		if (!empty($email) && newsletter_is_email_address($email)) {
			// ignore access
			$ia = elgg_set_ignore_access(true);
			
			$options = array(
				"type" => "object",
				"subtype" => NewsletterSubscription::SUBTYPE,
				"limit" => 1,
				"joins" => array("JOIN " . elgg_get_config("dbprefix") . "objects_entity oe ON e.guid = oe.guid"),
				"wheres" => array("(oe.title = '" . sanitise_string($email) . "')")
			);
			
			$entities = elgg_get_entities($options);
			
			if (!empty($entities)) {
				$result = $entities[0];
			}
			
			// restore access
			elgg_set_ignore_access($ia);
		}
		
		return $result;
	}
	
	/**
	 * Block an user from all newsletters
	 *
	 * @param 	ElggUser 	$user	The user to block
	 * @return 	boolean				True on success, false on failure
	 */
	function newsletter_unsubscribe_all_user(ElggUser $user) {
		$result = false;
		
		if (!empty($user) && elgg_instanceof($user, "user")) {
			$site = elgg_get_site_entity();
			// remove site subscription
			remove_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $site->getGUID());
			
			// remove all subscriptions
			$options = array(
				"type" => "group",
				"limit" => false,
				"relationship" => NewsletterSubscription::SUBSCRIPTION,
				"relationship_guid" => $user->getGUID(),
				"callback" => "newsletter_row_to_guid"
			);
			
			$entities = elgg_get_entities_from_relationship($options);
			
			if (!empty($entities)) {
				foreach ($entities as $entity_guid) {
					remove_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity_guid);
				}
			}
			
			// add to general blacklist
			$result = (bool) add_entity_relationship($user->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
			
			// remove email subscriptions (if any)
			$subscription = newsletter_get_subscription($user->email);
			
			if (!empty($subscription)) {
				$subscription->delete();
			}
		}
		
		return $result;
	}
	
	/**
	 * Block an email address from all newsletters
	 *
	 * @param 	string 	$email	The email address to block
	 * @return 	boolean			True on success, false on failure
	 */
	function newsletter_unsubscribe_all_email($email) {
		$result = false;
		
		if (!empty($email) && newsletter_is_email_address($email)) {
			// get subscription
			$subscription = newsletter_get_subscription($email);
			
			if (empty($subscription)) {
				$subscription = new NewsletterSubscription();
				$subscription->title = $email;
				
				if (!$subscription->save()) {
					return false;
				}
				
				// remove all existing subscriptions
				remove_entity_relationships($subscription->getGUID(), NewsletterSubscription::SUBSCRIPTION);
				
				// add to general blacklist
				$site = elgg_get_site_entity();
					
				$result = (bool) add_entity_relationship($subscription->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
			}
		}
		
		return $result;
	}
	
	/**
	 * Convert an email subscription on the newsletters to a user setting
	 *
	 * @param 	NewsletterSubscription 	$subscription	The found email subscription
	 * @param 	ElggUser 				$user			The user to save the new settings to
	 * @return 	bool									true on success or false on failure
	 */
	function newsletter_convert_subscription_to_user_setting(NewsletterSubscription $subscription, ElggUser $user) {
		$result = false;
		
		if (!empty($subscription) && elgg_instanceof($subscription, "object", NewsletterSubscription::SUBTYPE) && !empty($user) && elgg_instanceof($user, "user")) {
			// check global block list
			$site = elgg_get_site_entity();
			if (check_entity_relationship($subscription->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID())) {
				// copy the block all
				$result = (bool) add_entity_relationship($user->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
			} else {
				// check for subscriptions
				$subscriptions = $subscription->getEntitiesFromRelationship(NewsletterSubscription::SUBSCRIPTION, false, false);
					
				if (!empty($subscriptions)) {
					foreach ($subscriptions as $entity) {
						newsletter_subscribe_user($user, $entity);
					}
				}
					
				// check for blocks
				$blocked = $subscription->getEntitiesFromRelationship(NewsletterSubscription::BLACKLIST, false, false);
					
				if (!empty ($blocked)) {
					foreach ($blocked as $entity) {
						newsletter_unsubscribe_user($user, $entity);
					}
				}
			}
			
			// remove email subscription
			$result = (bool) $subscription->delete();
		}
		
		return $result;
	}
	
	function newsletter_is_group_enabled(ElggGroup $group = null) {
		static $plugin_setting;
		$result = false;
		
		// make sure we only get the plugin setting once
		if (!isset($plugin_setting)) {
			$plugin_setting = false;
			
			$setting = elgg_get_plugin_setting("allow_groups", "newsletter");
			if ($setting == "yes") {
				$plugin_setting = true;
			}
		}
		
		// check the setting of the group (if any)
		if ($plugin_setting) {
			if (!empty($group) && elgg_instanceof($group, "group")) {
				// check the setting of the group
				$group_setting = $group->newsletter_enable;
				
				if ($group_setting != "no") {
					$result = true;
				}
			} else {
				// no group just checking plugin setting
				$result = true;
			}
		}
		
		return $result;
	}
	