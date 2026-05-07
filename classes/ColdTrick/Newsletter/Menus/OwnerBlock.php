<?php

namespace ColdTrick\Newsletter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the owner_block menu
 */
class OwnerBlock {
	
	/**
	 * Add menu items to the group owner_block
	 *
	 * @param \Elgg\Event $event 'register', 'menu:owner_block'
	 *
	 * @return null|MenuItems
	 */
	public static function registerGroupItems(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggGroup) {
			return null;
		}
		
		if (!newsletter_is_group_enabled($entity)) {
			return null;
		}
		
		/** @var MenuItems $returnvalue */
		$returnvalue = $event->getValue();
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter',
			'text' => elgg_echo('collection:object:newsletter:group'),
			'href' => elgg_generate_url('collection:object:newsletter:group', [
				'guid' => $entity->guid,
			]),
		]);
		
		return $returnvalue;
	}
}
