<?php

namespace ColdTrick\Newsletter;

class User {

	/**
	 * Check if there is a email subscription for the user's email address
	 * If so, convert the settings to the user and remove the email subscription
	 *
	 * @param string $hook        Which hook was triggered
	 * @param string $type        What was the type of hook
	 * @param array  $returnvalue null
	 * @param array  $params      null
	 *
	 * @return 	void
	 */
	public static function convertEmailSubscriptionToUserSetting($hook, $type, $returnvalue, $params) {
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
	 * A hook fired during the registration proccess of the user
	 * Check if the user wants to receive site newsletters
	 *
	 * @param string $hook        Which hook was triggered
	 * @param string $type        What was the type of hook
	 * @param bool   $returnvalue you can stop the registration proccess
	 * @param array  $params      different variables, including the new user
	 *
	 * @return void
	 */
	public static function subscribeToSiteNewsletter($hook, $type, $returnvalue, $params) {
	
		$user = elgg_extract('user', $params);
		if (!elgg_instanceof($user, 'user')) {
			return;
		}
		
		$site = elgg_get_site_entity();
		$subscribe = (int) get_input('newsletter_subscription');
			
		if (!empty($subscribe)) {
			// user wants to receive newsletter
			newsletter_subscribe_user($user, $site);
		} else {
			// user doesn't want to recieve newsletter
			newsletter_unsubscribe_user($user, $site);
		}
	}
}