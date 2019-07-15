<?php
/**
 * Action file to save the subscriptions of a user
 */

$user_guid = (int) get_input('user_guid');
$subscriptions = get_input('subscriptions');
$block_all = (int) get_input('block_all');

if (empty($user_guid) || empty($subscriptions) || !is_array($subscriptions)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}
	
$user = get_user($user_guid);
if (empty($user) || !$user->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$result = true;

if (!empty($block_all)) {
	// block all newsletters
	if (newsletter_unsubscribe_all_user($user)) {
		return elgg_ok_response('', elgg_echo('newsletter:action:subscriptions:success'));
	}
	
	return elgg_error_response(elgg_echo('newsletter:action:subscriptions:error'));
}

// remove block all
remove_entity_relationship($user->guid, NewsletterSubscription::GENERAL_BLACKLIST, elgg_get_site_entity()->guid);

// go through all the subscriptions
foreach ($subscriptions as $guid => $value) {
	// value is stored
	// 1 => subscribe
	// 0 => unsubscribe
	$value = (bool) $value;
	$entity = get_entity($guid);
	if (!$entity instanceof ElggEntity) {
		continue;
	}
	
	if ($value) {
		$result = $result && newsletter_subscribe_user($user, $entity, false);
	} else {
		$result = $result && newsletter_unsubscribe_user($user, $entity);
	}
}

if (!$result) {
	return elgg_error_response(elgg_echo('newsletter:action:subscriptions:error'));
}

return elgg_ok_response('', elgg_echo('newsletter:action:subscriptions:success'));
