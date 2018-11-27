<?php

namespace ColdTrick\Newsletter;

class Widgets {

	/**
	 * Provide an URL for a widget title
	 *
	 * @param \Elgg\Hook $hook 'entity:url', 'object'
	 *
	 * @return string
	 */
	public static function widgetURL(\Elgg\Hook $hook) {
		
		$widget = $hook->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return;
		}
		
		if ($widget->handler !== 'newsletter_subscribe') {
			return;
		}
		
		$return = $hook->getValue();
		if (!empty($return)) {
			return;
		}
		
		$user = elgg_get_logged_in_user_entity();
		if (empty($user)) {
			return;
		}
		
		return elgg_generate_url('collection:object:newsletter:subscriptions', ['username' => $user->username]);
	}
}