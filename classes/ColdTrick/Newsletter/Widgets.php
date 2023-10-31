<?php

namespace ColdTrick\Newsletter;

/**
 * Widgets callbacks
 */
class Widgets {

	/**
	 * Provide a URL for a widget title
	 *
	 * @param \Elgg\Event $event 'entity:url', 'object'
	 *
	 * @return null|string
	 */
	public static function widgetURL(\Elgg\Event $event): ?string {
		$widget = $event->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return null;
		}
		
		if ($widget->handler !== 'newsletter_subscribe') {
			return null;
		}
		
		$return = $event->getValue();
		if (!empty($return)) {
			return null;
		}
		
		$user = elgg_get_logged_in_user_entity();
		if (empty($user)) {
			return null;
		}
		
		return elgg_generate_url('collection:object:newsletter:subscriptions', ['username' => $user->username]);
	}
}
