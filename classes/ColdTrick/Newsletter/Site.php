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

	/**
	 * Extend public pages
	 *
	 * @param string   $hook_name    'public_pages'
	 * @param string   $entity_type  'walled_garden'
	 * @param string[] $return_value array of public pages
	 * @param mixed    $params       unused
	 *
	 * @return string[]
	 */
	public static function publicPages($hook_name, $entity_type, $return_value, $params) {
		if (!is_array($return_value)) {
			return;
		}

		$return_value[] = 'newsletter/view/.*';
		$return_value[] = 'newsletter/unsubscribe/.*';
		$return_value[] = 'action/newsletter/unsubscribe';

		return $return_value;
	}
}