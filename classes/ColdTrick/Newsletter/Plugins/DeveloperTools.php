<?php

namespace ColdTrick\Newsletter\Plugins;

use Elgg\ViewsService;

/**
 * DeveloperTools related callbacks
 */
class DeveloperTools {
	
	/**
	 * Prevent the developer log output
	 *
	 * @param \Elgg\Event $event 'view_vars', 'page/layouts/newsletter'
	 *
	 * @return void
	 */
	public static function preventLogOutput(\Elgg\Event $event) {
		// prevent developer tools output
		elgg_register_event_handler('view_vars', 'developers/log', self::class . '::blockOutput');
	}
	
	/**
	 * Re-enable developer log output
	 *
	 * @param \Elgg\Event $event 'view', 'page/layouts/newsletter'
	 *
	 * @return void
	 */
	public static function reenableLogOutput(\Elgg\Event $event) {
		// re-enable developer tools output
		elgg_unregister_event_handler('view_vars', 'developers/log', self::class . '::blockOutput');
	}
	
	/**
	 * Prevent view output
	 *
	 * @param \Elgg\Event $event 'view_vars', 'developers/log'
	 *
	 * @return array
	 */
	public static function blockOutput(\Elgg\Event $event) {
		
		$vars = $event->getValue();
		$vars[ViewsService::OUTPUT_KEY] = '';
		
		return $vars;
	}
}
