<?php

$guid = (int) get_input("guid");
$user_guid = (int) get_input("user_guid");
$email = get_input("email");

if (!empty($guid) && (!empty($user_guid) || !empty($email))) {
	$entity = get_entity($guid);
	
	if (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group")) {
		if (!empty($user_guid)) {
			// check the user
			$user = get_user($user_guid);
			
			if (!empty($user) && $user->canEdit()) {
				// is the user subscribed
				if (newsletter_check_user_subscription($user, $entity)) {
					// unsubscribe
					if (newsletter_unsubscribe_user($user, $entity)) {
						system_message(elgg_echo("newsletter:action:subscribe:success:unsubscribe"));
					} else {
						register_error(elgg_echo("newsletter:action:subscribe:error:unsubscribe"));
					}
				} else {
					// subscribe
					if (newsletter_subscribe_user($user, $entity)) {
						system_message(elgg_echo("newsletter:action:subscribe:success"));
					} else {
						register_error(elgg_echo("newsletter:action:subscribe:error:subscribe"));
					}
				}
			} else {
				register_error(elgg_echo("user:resetpassword:unknown_user"));
			}
		} else {
			if (newsletter_is_email_address($email)) {
				// add the email address to the subscriber list
				if (newsletter_subscribe_email($email, $entity)) {
					system_message(elgg_echo("newsletter:action:subscribe:success"));
				} else {
					register_error(elgg_echo("newsletter:action:subscribe:error:subscribe"));
				}
			} else {
				register_error(elgg_echo("registration:notemail"));
			}
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward(REFERER);