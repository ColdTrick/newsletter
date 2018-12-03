<?php

namespace ColdTrick\Newsletter;

class Site {
	
	/**
	 * Listen to the join site event and check for email subscriptions
	 *
	 * @param string           $event  What event was fired
	 * @param string           $type   What was the type of event
	 * @param ElggRelationship $object The relationship object, containing the user and site
	 *
	 * @return void
	 */
	public static function join($event, $type, $object) {
		
		if (!($object instanceof \ElggRelationship)) {
			return;
		}
		
		if ($object->relationship !== 'member_of_site') {
			return;
		}
		
		$user = get_user($object->guid_one);
		if (empty($user)) {
			return;
		}
		
		$subscription = newsletter_get_subscription($user->email);
		if (!empty($subscription)) {
			newsletter_convert_subscription_to_user_setting($subscription, $user);
		}
	}
}