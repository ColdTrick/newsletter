<?php

namespace ColdTrick\Newsletter;

class Permissions {
	
	/**
	 * Prevent newsletter from being created in the wrong container
	 *
	 * @param \Elgg\Hook $hook 'container_logic_check', 'object'
	 *
	 * @return void|bool
	 */
	public static function containerLogic(\Elgg\Hook $hook) {
		
		if ($hook->getParam('subtype') !== \Newsletter::SUBTYPE) {
			return;
		}
		
		$user = $hook->getUserParam();
		if (!$user instanceof \ElggUser) {
			return false;
		}
		
		$container = $hook->getParam('container');
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
