<?php

$guid = (int) get_input('guid');
$user_guid = (int) get_input('user_guid');
$email = get_input('email');

if (empty($guid) || (empty($user_guid) && empty($email))) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof \ElggSite && !$entity instanceof \ElggGroup) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}
	
if (!empty($user_guid)) {
	// check the user
	$user = get_user($user_guid);
	
	if ($user instanceof \ElggUser && $user->canEdit()) {
		// is the user subscribed
		if (newsletter_check_user_subscription($user, $entity)) {
			// unsubscribe
			if (newsletter_unsubscribe_user($user, $entity)) {
				return elgg_ok_response('', elgg_echo('newsletter:action:subscribe:success:unsubscribe'));
			}
			
			return elgg_error_response(elgg_echo('newsletter:action:subscribe:error:unsubscribe'));
		}
		
		// subscribe
		if (newsletter_subscribe_user($user, $entity)) {
			return elgg_ok_response('', elgg_echo('newsletter:action:subscribe:success'));
		}
		
		return elgg_error_response(elgg_echo('newsletter:action:subscribe:error:subscribe'));
	}
	
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!newsletter_is_email_address($email)) {
	return elgg_error_response(elgg_echo('registration:notemail'));
}

// add the email address to the subscriber list
if (!newsletter_subscribe_email($email, $entity)) {
	return elgg_error_response(elgg_echo('newsletter:action:subscribe:error:subscribe'));
}

return elgg_ok_response('', elgg_echo('newsletter:action:subscribe:success'));
