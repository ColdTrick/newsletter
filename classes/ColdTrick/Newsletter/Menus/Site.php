<?php

namespace ColdTrick\Newsletter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the site menu
 */
class Site {
	
	/**
	 * Add menu item to the site menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:site'
	 *
	 * @return null|MenuItems
	 */
	public static function register(\Elgg\Event $event): ?MenuItems {
		/* @var $returnvalue MenuItems */
		$returnvalue = $event->getValue();
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter',
			'icon' => 'mail-bulk',
			'text' => elgg_echo('newsletter:menu:site'),
			'href' => elgg_generate_url('default:object:newsletter'),
		]);
		
		return $returnvalue;
	}
}
