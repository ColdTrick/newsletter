<?php
/**
 * Unsubscribe the provided recipient from the newsletter(s)
 */

$guid = (int) get_input('guid');
$recipient = get_input('recipient');
$all = (int) get_input('all');

$code = get_input('code');
$entity_guid = (int) get_input('entity_guid');

if (empty($entity_guid) || empty($recipient)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($entity_guid);
if (empty($entity) || !(empty($code) || newsletter_validate_unsubscribe_code($entity, $recipient, $code))) {
	return elgg_error_response(elgg_echo('newsletter:unsubscribe:error:code'));
}

$user = false;
$error = false;

// what to unsubscribe
if (!empty($guid)) {
	// unsubscribe one newsletter
	if (is_numeric($recipient) && ($user = get_user($recipient))) {
		if (newsletter_unsubscribe_user($user, $entity)) {
			system_message(elgg_echo('newsletter:action:unsubscribe:success:entity', [$entity->getDisplayName()]));
		} else {
			$error = true;
			register_error(elgg_echo('newsletter:action:unsubscribe:error:entity', [$entity->getDisplayName()]));
		}
	} elseif (newsletter_is_email_address($recipient)) {
		if (newsletter_unsubscribe_email($recipient, $entity)) {
			system_message(elgg_echo('newsletter:action:unsubscribe:success:entity', [$entity->getDisplayName()]));
		} else {
			$error = true;
			register_error(elgg_echo('newsletter:action:unsubscribe:error:entity', [$entity->getDisplayName()]));
		}
	} else {
		$error = true;
		register_error(elgg_echo('newsletter:action:unsubscribe:error:recipient', [$recipient]));
	}
}

// unsubscribe from all
if (!empty($all)) {
	if (is_numeric($recipient) && ($user = get_user($recipient))) {
		if (newsletter_unsubscribe_all_user($user)) {
			system_message(elgg_echo('newsletter:action:unsubscribe:success:all'));
		} else {
			$error = true;
			register_error(elgg_echo('newsletter:action:unsubscribe:error:all'));
		}
	} elseif (newsletter_is_email_address($recipient)) {
		if (newsletter_unsubscribe_all_email($recipient)) {
			system_message(elgg_echo('newsletter:action:unsubscribe:success:all'));
		} else {
			$error = true;
			register_error(elgg_echo('newsletter:action:unsubscribe:error:all'));
		}
	} else {
		$error = true;
		register_error(elgg_echo('newsletter:action:unsubscribe:error:recipient', [$recipient]));
	}
}

if ($error) {
	return elgg_error_response();
} elseif ($user instanceof ElggUser) {
	return elgg_ok_response('', '', elgg_generate_url('collection:object:newsletter:subscriptions', [
		'username' => $user->username,
	]));
}

return elgg_ok_response('', '', '');
