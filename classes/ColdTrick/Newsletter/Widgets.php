<?php

namespace ColdTrick\Newsletter;

/**
 * Widgets callbacks
 */
class Widgets {

	/**
	 * Provide an URL for a widget title
	 *
	 * @param \Elgg\Event $event 'entity:url', 'object'
	 *
	 * @return string
	 */
	public static function widgetURL(\Elgg\Event $event) {
		
		$widget = $event->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return;
		}
		
		if ($widget->handler !== 'newsletter_subscribe') {
			return;
		}
		
		$return = $event->getValue();
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
