<?php

namespace ColdTrick\Newsletter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the entity menu
 */
class Entity {
	
	/**
	 * Add menu items to the newsletter entity menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return null|MenuItems
	 */
	public static function registerNewsletter(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \Newsletter || !$entity->canEdit()) {
			return null;
		}
		
		/* @var $returnvalue MenuItems */
		$returnvalue = $event->getValue();
		if ($entity->status === 'sent' || $entity->getLogging()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'log',
				'icon' => 'file-alt',
				'text' => elgg_echo('newsletter:menu:entity:log'),
				'href' => elgg_generate_entity_url($entity, 'log'),
			]);
		}
		
		$container = $entity->getContainerEntity();
		if ($container->canWriteToContainer(0, 'object', \Newsletter::SUBTYPE)) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'duplicate',
				'icon' => 'copy',
				'text' => elgg_echo('newsletter:menu:entity:duplicate'),
				'href' => elgg_generate_action_url('newsletter/duplicate', [
					'guid' => $entity->guid,
				]),
			]);
		}
		
		return $returnvalue;
	}
}
