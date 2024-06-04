<?php

namespace ColdTrick\Newsletter;

/**
 * Permissions callbacks
 */
class Permissions {
	
	/**
	 * Prevent newsletter from being created in the wrong container
	 *
	 * @param \Elgg\Event $event 'container_logic_check', 'object'
	 *
	 * @return void|bool
	 */
	public static function containerLogic(\Elgg\Event $event) {
		if ($event->getParam('subtype') !== \Newsletter::SUBTYPE) {
			return;
		}
		
		$user = $event->getUserParam();
		if (!$user instanceof \ElggUser) {
			return false;
		}
		
		$container = $event->getParam('container');
		if ($container instanceof \ElggSite) {
			if ($user->isAdmin() && elgg_get_plugin_setting('allow_site', 'newsletter') === 'yes') {
				return true;
			}
		} elseif ($container instanceof \ElggGroup) {
			if ($container->canEdit($user->guid) && newsletter_is_group_enabled($container)) {
				return true;
			}
		}
		
		return false;
	}
}
