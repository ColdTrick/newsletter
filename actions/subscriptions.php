<?php

	/**
	 * Action file to save the subscriptions of a user
	 *
	 */

	$user_guid = (int) get_input("user_guid");
	$subscriptions = get_input("subscriptions");
	
	if (!empty($user_guid) && !empty($subscriptions) && is_array($subscriptions)) {
		$user = get_user($user_guid);
		
		if (!empty($user) && $user->canEdit()) {
			$result = true;
			
			// go through all the subscriptions
			foreach ($subscriptions as $guid => $value) {
				// value is stored
				// 1 => subscribe
				// 0 => unsubscribe
				$value = (bool) $value;
				$entity = get_entity($guid);
				
				if (!empty($entity)) {
					if ($value) {
						$result = $result && newsletter_subscribe_user($user, $entity);
					} else {
						$result = $result && newsletter_unsubscribe_user($user, $entity);
					}
				}
			}
			
			if ($result) {
				system_message(elgg_echo("newsletter:action:subscriptions:success"));
			} else {
				register_error(elgg_echo("newsletter:action:subscriptions:error"));
			}
		} else {
			register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward(REFERER);