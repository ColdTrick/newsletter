<?php

namespace ColdTrick\Newsletter;

class Widgets {

	/**
	 * Provide an URL for a widget title
	 *
	 * @param string $hook_name    'widget_url'
	 * @param string $entity_type  'widget_manager'
	 * @param string $return_value the current url (if any)
	 * @param mixed  $params       provided params
	 *
	 * @return string
	 */
	public static function widgetURL($hook_name, $entity_type, $return_value, $params) {
		if (!empty($return_value)) {
			return;
		}
		
		$user = elgg_get_logged_in_user_entity();
		if (empty($user)) {
			return;
		}
		
		$widget = elgg_extract('entity', $params);

		if (!elgg_instanceof($widget, 'object', 'widget')) {
			return;
		}
		
		if ($widget->handler == 'newsletter_subscribe') {
			return elgg_normalize_url("newsletter/subscriptions/{$user->username}");
		}
	}
}