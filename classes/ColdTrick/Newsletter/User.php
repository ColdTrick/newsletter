<?php

namespace ColdTrick\Newsletter;

/**
 * User callbacks
 */
class User {

	/**
	 * Check if there is a email subscription for the user's email address
	 * If so, convert the settings to the user and remove the email subscription
	 *
	 * @param \Elgg\Event $event 'usersettings:save', 'user'
	 *
	 * @return void
	 */
	public static function convertEmailSubscriptionToUserSetting(\Elgg\Event $event) {
		$user_guid = (int) get_input('guid');
	
		$user = get_user($user_guid);
		if (empty($user)) {
			return;
		}
		
		$subscription = newsletter_get_subscription($user->email);
		if (!empty($subscription)) {
			newsletter_convert_subscription_to_user_setting($subscription, $user);
		}
	}
	
	/**
	 * An event fired during the registration process of the user
	 * Check if the user wants to receive site newsletters
	 *
	 * @param \Elgg\Event $event 'register', 'user'
	 *
	 * @return void
	 */
	public static function subscribeToSiteNewsletter(\Elgg\Event $event) {
		$user = $event->getUserParam();
		if (!$user instanceof \ElggUser) {
			return;
		}
		
		// convert potential e-maul subscription to user subscription
		$subscription = newsletter_get_subscription($user->email);
		if (!empty($subscription)) {
			newsletter_convert_subscription_to_user_setting($subscription, $user);
		}
		
		// check for site subscription
		if (elgg_get_plugin_setting('allow_site', 'newsletter') !== 'yes') {
			return;
		}
		
		$site = elgg_get_site_entity();
		$subscribe = (int) get_input('newsletter_subscription');
			
		if (!empty($subscribe)) {
			// user wants to receive newsletter
			newsletter_subscribe_user($user, $site);
		} else {
			// user doesn't want to receive newsletter
			newsletter_unsubscribe_user($user, $site);
		}
	}
}
