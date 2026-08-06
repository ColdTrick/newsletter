<?php
/**
 * This file contains all supportive functions for the Newsletter plugin
 */

use Elgg\Database\QueryBuilder;
use Elgg\Database\RelationshipsTable;
use Elgg\Email;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Address;

/**
 * Start the commandline to send a newsletter
 * This is offloaded because it could take a while and/or resources
 *
 * @param Newsletter $entity Newsletter entity to be processed
 *
 * @return void
 */
function newsletter_start_commandline_sending(\Newsletter $entity): void {
	// prepare commandline settings
	$settings = [
		'entity_guid' => $entity->guid,
		'memory_limit' => ini_get('memory_limit'),
		'secret' => newsletter_generate_commandline_secret($entity->guid),
	];
	if (isset($_SERVER['HTTP_HOST'])) {
		$settings['host'] = $_SERVER['HTTP_HOST'];
	}
	
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
	$script_location = dirname(__FILE__, 2) . '/procedures/cli.php';
	
	// convert settings to commandline params
	$query_string = http_build_query($settings, '', ' ');
	
	// start the correct commandline
	if (PHP_OS_FAMILY === 'Windows') {
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
 * @return null|string
 */
function newsletter_generate_commandline_secret(int $entity_guid): ?string {
	if ($entity_guid < 1) {
		return null;
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
 * @return bool
 */
function newsletter_validate_commandline_secret(int $entity_guid, string $secret): bool {
	if ($entity_guid < 1 || empty($secret)) {
		return false;
	}
	
	$correct_secret = newsletter_generate_commandline_secret($entity_guid);
	if (empty($correct_secret) || $correct_secret !== $secret) {
		return false;
	}
	
	return true;
}

/**
 * Format an e-mail recipient to be listed in the recipient autocomplete or listing
 *
 * @param string $recipient email address
 *
 * @return null|array Contains, or null on failure
 * 						- type : email
 * 						- label: used in the autocomplete dropdown
 * 						- html: used in the listing view
 * 						- value: email address
 */
function newsletter_format_email_recipient(string $recipient): ?array {
	if (!newsletter_is_email_address($recipient)) {
		return null;
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
 * Check if a user is subscribed to a container entity
 *
 * @param \ElggUser   $user   The user to check
 * @param \ElggEntity $entity The container entity to check against
 *
 * @return bool
 */
function newsletter_check_user_subscription(\ElggUser $user, \ElggEntity $entity): bool {
	if (!$entity instanceof \ElggSite && !$entity instanceof \ElggGroup) {
		return false;
	}
	
	// include all users
	if (elgg_get_plugin_setting('include_existing_users', 'newsletter') === 'yes') {
		// exclude if blocked
		return !$user->hasRelationship($entity->guid, NewsletterSubscription::BLACKLIST);
	}
	
	// only if opt-in
	return $user->hasRelationship($entity->guid, NewsletterSubscription::SUBSCRIPTION);
}

/**
 * Add a subscription for a user to a container
 *
 * @param \ElggUser   $user                  The user to subscribe
 * @param \ElggEntity $entity                The container entity to subscribe to
 * @param bool        $cleanup_general_block Remove the generic block all (default: true)
 *
 * @return bool
 */
function newsletter_subscribe_user(\ElggUser $user, \ElggEntity $entity, bool $cleanup_general_block = true): bool {
	if (!$entity instanceof ElggSite && !$entity instanceof ElggGroup) {
		return false;
	}
	
	// check if subscribed
	if (!$user->hasRelationship($entity->guid, NewsletterSubscription::SUBSCRIPTION)) {
		// not yet, so add
		$result = $user->addRelationship($entity->guid, NewsletterSubscription::SUBSCRIPTION);
	} else {
		$result = true;
	}
	
	// remove blocklist relation
	$user->removeRelationship($entity->guid, NewsletterSubscription::BLACKLIST);
	
	// remove general blocklist
	if ($cleanup_general_block) {
		$site = elgg_get_site_entity();
		$user->removeRelationship($site->guid, NewsletterSubscription::GENERAL_BLACKLIST);
	}
	
	// check if on email blacklist
	$subscription = newsletter_get_subscription($user->email);
	
	if (!empty($subscription)) {
		$subscription->removeRelationship($entity->guid, NewsletterSubscription::BLACKLIST);
	}
	
	return $result;
}

/**
 * Add a subscription for an email address to a container
 *
 * @param string      $email  The email address to add to the subscriptions
 * @param \ElggEntity $entity The container entity to subscribe to
 *
 * @return bool
 */
function newsletter_subscribe_email(string $email, \ElggEntity $entity): bool {
	if (!newsletter_is_email_address($email)) {
		return false;
	}
	
	if (!$entity instanceof \ElggSite && !$entity instanceof \ElggGroup) {
		return false;
	}
	
	// check if email belongs to existing user
	$user = elgg_get_user_by_email($email);
	if (!empty($user)) {
		return newsletter_subscribe_user($user, $entity);
	}

	// check if email address exists in the system
	$subscription = newsletter_get_subscription($email);
	
	if (empty($subscription)) {
		$subscription = new \NewsletterSubscription();
		$subscription->title = $email;
		
		if (!$subscription->save()) {
			return false;
		}
	}
	
	// subscribe
	$result = (bool) $subscription->addRelationship($entity->guid, \NewsletterSubscription::SUBSCRIPTION);
	
	// remove blocklist relation
	$subscription->removeRelationship($entity->guid, \NewsletterSubscription::BLACKLIST);
	
	// remove general blocklist
	$subscription->removeRelationship(elgg_get_site_entity()->guid, \NewsletterSubscription::GENERAL_BLACKLIST);
	
	return $result;
}

/**
 * Remove a subscription for an user to a container
 *
 * @param \ElggUser   $user   The user to unsubscribe
 * @param \ElggEntity $entity The container entity to unsubscribe from
 *
 * @return bool
 */
function newsletter_unsubscribe_user(\ElggUser $user, \ElggEntity $entity): bool {
	if (!$entity instanceof \ElggSite && !$entity instanceof \ElggGroup) {
		return false;
	}
	
	// remove subscription
	$user->removeRelationship($entity->guid, \NewsletterSubscription::SUBSCRIPTION);
	
	// check if on email subscription list
	$subscription = newsletter_get_subscription($user->email);
	
	if (!empty($subscription)) {
		$subscription->removeRelationship($entity->guid, \NewsletterSubscription::SUBSCRIPTION);
	}
	
	// check if blocked
	if (!$user->hasRelationship($entity->guid, \NewsletterSubscription::BLACKLIST)) {
		// not yet, so add
		return $user->addRelationship($entity->guid, \NewsletterSubscription::BLACKLIST);
	}
	
	return true;
}

/**
 * Remove a subscription for an email address to a container
 *
 * @param string      $email  The email address to remove from the subscriptions
 * @param \ElggEntity $entity The container entity to unsubscribe from
 *
 * @return bool
 */
function newsletter_unsubscribe_email(string $email, \ElggEntity $entity): bool {
	if (!newsletter_is_email_address($email)) {
		return false;
	}
	
	if (!$entity instanceof \ElggSite && !$entity instanceof \ElggGroup) {
		return false;
	}
	
	// check if not existing user
	$user = elgg_get_user_by_email($email);
	if (!empty($user)) {
		// existing user
		return newsletter_unsubscribe_user($user, $entity);
	}
	
	// email address
	$subscription = newsletter_get_subscription($email);
	
	if (empty($subscription)) {
		$subscription = new \NewsletterSubscription();
		$subscription->title = $email;
		
		if (!$subscription->save()) {
			return false;
		}
	}
	
	// remove existing subscription (if any)
	$subscription->removeRelationship($entity->guid, \NewsletterSubscription::SUBSCRIPTION);
	
	// check if blocked
	if (!$subscription->hasRelationship($entity->guid, \NewsletterSubscription::BLACKLIST)) {
		// not yet, so add
		return $subscription->addRelationship($entity->guid, \NewsletterSubscription::BLACKLIST);
	}
	
	return true;
}

/**
 * A different interpretation of elgg_is_valid_email()
 * because PHP doesn't always correctly verify email addresses
 *
 * @param string $address The email address to check
 *
 * @return bool
 *
 * @see elgg_is_valid_email()
 * @see filter_var()
 */
function newsletter_is_email_address($address): bool {
	if (empty($address) || !is_string($address)) {
		// can't use typehinting as this function is used for input validation
		return false;
	}
	
	$regexpr = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
	
	return (bool) preg_match($regexpr, $address);
}

/**
 * Generate a URL so the recipient can directly unsubscribe from a newsletter
 *
 * @param \ElggEntity $container Which newsletter container (ElggSite or ElggGroup)
 * @param string|int  $recipient The user_guid or email address of the recipient
 *
 * @return null|string
 */
function newsletter_generate_unsubscribe_link(\ElggEntity $container, $recipient): ?string {
	if (!$container instanceof \ElggSite && !$container instanceof \ElggGroup) {
		return null;
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
 * Generate an unsubscribe code to be used in validation
 *
 * @param \ElggEntity $container Which newsletter container (ElggSite or ElggGroup)
 * @param string|int  $recipient The user_guid or email address of the recipient
 *
 * @return null|string
 */
function newsletter_generate_unsubscribe_code(\ElggEntity $container, $recipient): ?string {
	if (!$container instanceof \ElggSite && !$container instanceof \ElggGroup) {
		return null;
	}
	
	if (empty($recipient)) {
		return null;
	}
	
	// make sure we have a user_guid or email address
	if (!is_numeric($recipient) && !newsletter_is_email_address($recipient)) {
		return null;
	}
	
	if (is_numeric($recipient)) {
		$recipient = (int) $recipient;
	}
	
	$plugin = elgg_get_plugin_from_id('newsletter');
	$hmac = elgg_build_hmac([
		$container->guid,
		$recipient,
		$plugin->time_created,
	]);
	return $hmac->getToken();
}

/**
 * Validate a provided unsubscribe code
 *
 * @param \ElggEntity $container Which newsletter container (ElggSite or ElggGroup)
 * @param string|int  $recipient The user_guid or email address of the recipient
 * @param string      $code      The unsubscribe code the recipient provided
 *
 * @return bool
 */
function newsletter_validate_unsubscribe_code(\ElggEntity $container, $recipient, string $code): bool {
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
	return ($code === $correct_code);
}

/**
 * Find a subscription entity for the given email address
 *
 * @param string $email The email address to find the subscription for
 *
 * @return null|\NewsletterSubscription
 */
function newsletter_get_subscription(string $email): ?\NewsletterSubscription {
	if (!newsletter_is_email_address($email)) {
		return null;
	}
	
	// ignore access
	$entities = elgg_call(ELGG_IGNORE_ACCESS, function() use ($email) {
		return elgg_get_entities([
			'type' => 'object',
			'subtype' => \NewsletterSubscription::SUBTYPE,
			'limit' => 1,
			'metadata_name_value_pairs' => [
				'name' => 'title',
				'value' => $email,
			],
		]);
	});
	
	return $entities ? $entities[0] : null;
}

/**
 * Block a user from all newsletters
 *
 * @param \ElggUser $user The user to block
 *
 * @return bool
 */
function newsletter_unsubscribe_all_user(\ElggUser $user): bool {
	$site = elgg_get_site_entity();
	
	// remove site subscription
	$user->removeRelationship($site->guid, \NewsletterSubscription::SUBSCRIPTION);
	
	// remove all subscriptions
	$entities = elgg_get_entities([
		'type' => 'group',
		'limit' => false,
		'relationship' => \NewsletterSubscription::SUBSCRIPTION,
		'relationship_guid' => $user->guid,
		'callback' => function($row) {
			return (int) $row->guid;
		},
	]);
	
	if (!empty($entities)) {
		foreach ($entities as $entity_guid) {
			$user->removeRelationship($entity_guid, \NewsletterSubscription::SUBSCRIPTION);
		}
	}
	
	// add to general blacklist
	if (!$user->hasRelationship($site->guid, \NewsletterSubscription::GENERAL_BLACKLIST)) {
		$result = $user->addRelationship($site->guid, \NewsletterSubscription::GENERAL_BLACKLIST);
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
 * @return bool
 */
function newsletter_unsubscribe_all_email(string $email): bool {
	if (!newsletter_is_email_address($email)) {
		return false;
	}
	
	$subscription = newsletter_get_subscription($email);
	if (empty($subscription)) {
		$subscription = new \NewsletterSubscription();
		$subscription->title = $email;
		
		if (!$subscription->save()) {
			return false;
		}
	}
	
	// remove all existing subscriptions
	$subscription->removeAllRelationships(\NewsletterSubscription::SUBSCRIPTION);
	
	// add to general blacklist
	$site = elgg_get_site_entity();
	
	if ($subscription->hasRelationship($site->guid, \NewsletterSubscription::GENERAL_BLACKLIST)) {
		// already blocked
		return true;
	}
	
	return $subscription->addRelationship($site->guid, \NewsletterSubscription::GENERAL_BLACKLIST);
}

/**
 * Convert an email subscription on the newsletters to a user setting
 *
 * @param \NewsletterSubscription $subscription The found email subscription
 * @param \ElggUser               $user         The user to save the new settings to
 *
 * @return bool
 */
function newsletter_convert_subscription_to_user_setting(\NewsletterSubscription $subscription, \ElggUser $user): bool {
	// check global block list
	$site = elgg_get_site_entity();
	if ($subscription->hasRelationship($site->guid, \NewsletterSubscription::GENERAL_BLACKLIST)) {
		// copy the block all
		$user->addRelationship($site->guid, \NewsletterSubscription::GENERAL_BLACKLIST);
	} else {
		// check for subscriptions
		$subscriptions = $subscription->getEntitiesFromRelationship([
			'relationship' => \NewsletterSubscription::SUBSCRIPTION,
			'limit' => false,
			'batch' => true,
		]);
		
		foreach ($subscriptions as $entity) {
			newsletter_subscribe_user($user, $entity);
		}
			
		// check for blocks
		$blocked = $subscription->getEntitiesFromRelationship([
			'relationship' => \NewsletterSubscription::BLACKLIST,
			'limit' => false,
			'batch' => true,
		]);
			
		foreach ($blocked as $entity) {
			newsletter_unsubscribe_user($user, $entity);
		}
	}
	
	// remove email subscription
	return $subscription->delete();
}

/**
 * Checks if group newsletter is allowed
 *
 * @param null|\ElggGroup $group group entity
 *
 * @return bool
 */
function newsletter_is_group_enabled(?\ElggGroup $group = null): bool {
	static $plugin_setting;
	
	// make sure we only get the plugin setting once
	if (!isset($plugin_setting)) {
		$plugin_setting = elgg_get_plugin_setting('allow_groups', 'newsletter') === 'yes';
	}
	
	// check the setting of the group (if any)
	if (!$plugin_setting) {
		return false;
	}
	
	if (!$group instanceof \ElggGroup) {
		// no group just checking plugin setting
		return true;
	}
	
	// check the setting of the group
	return $group->isToolEnabled('newsletter');
}

/**
 * Sends the preview newsletter
 *
 * @param \Newsletter $entity newsletter to be sent
 * @param string      $email  email-address of the recipient
 *
 * @return bool
 */
function newsletter_send_preview(\Newsletter $entity, string $email): bool {
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
	$message_html_content = str_ireplace(urlencode('{unsublink}'), $unsubscribe_link, $message_html_content);
	
	// replace online link
	$online_link = $entity->getURL();
	$new_online_link = $online_link . '?e=' . $email;
	
	$message_html_content = str_ireplace($online_link, $new_online_link, $message_html_content);
	
	// apply url postfix
	$message_html_content = newsletter_apply_url_postfix($message_html_content, $entity);
	
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
 * Other plugins/themes can provide their own template if they create a view
 * newsletter/templates/<some name>/{body|css}
 *
 * @param int              $container_guid The container of the current newsletter
 * @param null|\ElggEntity $entity         The current newsletter (optional) if editing
 *
 * @return array The available templates
 */
function newsletter_get_available_templates(int $container_guid, ?\ElggEntity $entity = null): array {
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
			'subtype' => \NewsletterTemplate::SUBTYPE,
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
		
	return elgg_trigger_event_results('templates', 'newsletter', [
		'container_guid' => $container_guid,
		'entity' => $entity,
	], $result);
}

/**
 * Process an uploaded CSV file to find new recipients.
 *
 * @param array $recipients previous recipients, to prevent duplicates
 *
 *                          Contains:
 *                          - user_guids => array() existing users
 *                          - emails => array() extra email addresses
 *
 * @return array
 */
function newsletter_process_csv_upload(array $recipients): array {
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
		
		$row = $fh->fgetcsv(';', '\'', '\\');
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
		elgg_register_success_message(elgg_echo('newsletter:csv:no_email'));
		
		return $recipients;
	}
	
	$counter = 0;
	
	// start at the beginning
	$fh->rewind();
	
	while (!$fh->eof()) {
		$row = $fh->fgetcsv(';', '"', '\\');
		
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
		$user = elgg_call(ELGG_IGNORE_ACCESS, function () use ($email) {
			return elgg_get_user_by_email($email);
		});
		if (!empty($user)) {
			if (in_array($user->guid, $recipients['user_guids'])) {
				continue;
			}
		}
		
		// email address wasn't added yet
		// so add to the list
		$user = elgg_call(ELGG_IGNORE_ACCESS, function () use ($email) {
			return elgg_get_user_by_email($email);
		});
		if (!empty($user)) {
			$recipients['user_guids'][] = $user->guid;
		} else {
			$recipients['emails'][] = $email;
		}
	}
	
	// done, report the added emails
	elgg_register_success_message(elgg_echo('newsletter:csv:added', [$counter]));
	
	return $recipients;
}

/**
 * Get the plugin settings for URL postfix
 *
 * @return array
 */
function newsletter_get_url_postfix(): array {
	static $result;
	
	if (isset($result)) {
		return $result;
	}
	
	$result = [];
	
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
 * Add the URL postfix to all urls in the newsletter content
 *
 * @param string     $html_content the content of the newsletter
 * @param Newsletter $newsletter   the source newsletter to use for some replacements in the postfix
 *
 * @return string
 */
function newsletter_apply_url_postfix(string $html_content, \Newsletter $newsletter): string {
	static $pattern;
	
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
		$preg_site_url = str_replace('/', '\\/', $preg_site_url);
		$preg_site_url = str_replace('.', '\\.', $preg_site_url);
		
		$pattern = '/\shref=([\'"]' . $preg_site_url . '[^\'"]*[\'"])/i';
	}
	
	// find all matches
	$matches = [];
	preg_match_all($pattern, $html_content, $matches);
	
	if (empty($matches) || !isset($matches[1])) {
		return $html_content;
	}
	
	// url postfix placeholder replacements
	$replacements = ['{guid}' => $newsletter->guid];
	
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
 * Display content in the correct layout for embedding in Newsletter
 *
 * @param ElggEntity $entity the entity to embed
 * @param array      $vars   optional variables to pass to the embed view
 *
 * @return string
 */
function newsletter_view_embed_content(\ElggEntity $entity, array $vars = []): string {
	$vars['entity'] = $entity;
	
	$type = $entity->getType();
	$subtype = $entity->getSubtype();
	
	if (elgg_view_exists("newsletter/embed/{$type}/{$subtype}")) {
		return elgg_view("newsletter/embed/{$type}/{$subtype}", $vars);
	} elseif (elgg_view_exists("newsletter/embed/{$type}/default")) {
		return elgg_view("newsletter/embed/{$type}/default", $vars);
	} elseif (elgg_view_exists('newsletter/embed/default')) {
		return elgg_view('newsletter/embed/default', $vars);
	}
	
	return '';
}

/**
 * Validate the custom from email address
 *
 * This also triggers a plugin event 'from_email', 'newsletter' for other plugins to hook into
 * Supplied params:
 * - email: the email address to validate
 *
 * @param string $from_email the email address to check
 *
 * @return bool
 */
function newsletter_validate_custom_from(string $from_email): bool {
	if (empty($from_email)) {
		// empty is allowed, sending will fall back to container
		return true;
	}
	
	if (!newsletter_is_email_address($from_email)) {
		// not an email address, always fail
		return false;
	}
	
	$result = true;
	
	// check plugin settings domain limitations
	$plugin_setting = (string) elgg_get_plugin_setting('custom_from_domains', 'newsletter');
	if (!empty($plugin_setting)) {
		$result = false;
		$plugin_setting = elgg_string_to_array($plugin_setting);
		
		list(, $domain) = explode('@', $from_email);
		foreach ($plugin_setting as $allowed_domain) {
			if ($domain === $allowed_domain) {
				// custom from is from an allowed domain
				$result = true;
				break;
			}
		}
	}
	
	// trigger a plugin event so others are allowed to validate
	return (bool) elgg_trigger_event_results('from_email', 'newsletter', ['email' => $from_email], $result);
}

/**
 * Register newsletter subscription items to the title menu
 *
 * @param \ElggEntity $container_entity for which container entity to check subscriptions
 *
 * @return void
 */
function newsletter_register_title_menu_items(\ElggEntity $container_entity): void {
	if (!$container_entity instanceof \ElggSite && !$container_entity instanceof \ElggGroup) {
		return;
	}
	
	if ($container_entity instanceof \ElggGroup && !$container_entity->isToolEnabled('newsletter')) {
		return;
	}
	
	$user = elgg_get_logged_in_user_entity();
	if ($user instanceof \ElggUser) {
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
			'priority' => 550,
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
			'priority' => 551,
		]);
	} else {
		// logged out user
		elgg_register_menu_item('title', [
			'name' => 'newsletter_subscribe',
			'icon' => 'envelope-open-text',
			'text' => elgg_echo("newsletter:subscribe:{$container_entity->type}"),
			'title' => elgg_echo('newsletter:subscribe:user:description:subscribe', [$container_entity->getDisplayName()]),
			'href' => elgg_generate_url('ajax', [
				'type' => 'form',
				'segments' => 'newsletter/subscribe',
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
