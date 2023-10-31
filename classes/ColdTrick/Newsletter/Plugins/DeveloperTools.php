<?php

namespace ColdTrick\Newsletter\Plugins;

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
		elgg_register_event_handler('view_vars', 'developers/log', '\Elgg\Values::preventViewOutput');
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
		elgg_unregister_event_handler('view_vars', 'developers/log', '\Elgg\Values::preventViewOutput');
	}
}
