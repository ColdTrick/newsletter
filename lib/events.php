<?php
/**
 * All event handler functions can be found in this file
 */

/**
 * Listen to the join site event and check for email subscriptions
 *
 * @param string           $event  What event was fired
 * @param string           $type   What was the type of event
 * @param ElggRelationship $object The relationship object, containing the user and site
 *
 * @return void
 */
function newsletter_join_site_event_handler($event, $type, $object) {
	
	if (!empty($object) && ($object instanceof ElggRelationship)) {
		$user = get_user($object->guid_one);
		
		if (!empty($user)) {
			$subscription = newsletter_get_subscription($user->email);
			
			if (!empty($subscription)) {
				newsletter_convert_subscription_to_user_setting($subscription, $user);
			}
		}
	}
}
