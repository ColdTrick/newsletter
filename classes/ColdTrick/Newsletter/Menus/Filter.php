<?php

namespace ColdTrick\Newsletter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the filter menu
 */
class Filter {
	
	/**
	 * Add newsletter filter menu items
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:newsletter[/group]'
	 *
	 * @return null|MenuItems
	 */
	public static function registerNewsletter(\Elgg\Event $event): ?MenuItems {
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup && !$page_owner->canEdit()) {
			return null;
		}
		
		if (empty($page_owner) && !elgg_is_admin_logged_in()) {
			return null;
		}
		
		$route_name = 'collection:object:newsletter:site';
		$route_params = [];
		if ($page_owner instanceof \ElggGroup) {
			$route_name = 'collection:object:newsletter:group';
			$route_params['guid'] = $page_owner->guid;
		}
		
		$current_filter = $event->getParam('filter_value', 'sent');
		
		/* @var $returnvalue MenuItems */
		$returnvalue = $event->getValue();
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'concept',
			'text' => elgg_echo('newsletter:menu:filter:concept'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'concept',
			]),
			'selected' => ($current_filter == 'concept'),
		]);
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'scheduled',
			'text' => elgg_echo('newsletter:menu:filter:scheduled'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'scheduled',
			]),
			'selected' => ($current_filter == 'scheduled'),
		]);
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sending',
			'text' => elgg_echo('newsletter:menu:filter:sending'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'sending',
			]),
			'selected' => ($current_filter == 'sending'),
		]);
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sent',
			'text' => elgg_echo('newsletter:menu:filter:sent'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'sent',
			]),
			'selected' => ($current_filter == 'sent'),
		]);
		
		return $returnvalue;
	}
	
	/**
	 * Add a menu items to the filter menu for the different steps of a newsletter
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:newsletter_steps'
	 *
	 * @return MenuItems
	 */
	public static function registerNewsletterSteps(\Elgg\Event $event): MenuItems {
		$entity = $event->getParam('filter_entity', $event->getEntityParam());
		
		/* @var $returnvalue MenuItems */
		$returnvalue = $event->getValue();
		if ($entity instanceof \Newsletter) {
			$current_step = $event->getParam('filter_value');
			
			// basic info
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'basic',
				'icon' => 'check',
				'text' => elgg_echo('newsletter:menu:steps:entity'),
				'href' => elgg_generate_entity_url($entity, 'edit'),
				'selected' => $current_step === 'basic',
			]);
			
			// template
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'template',
				'icon' => $entity->template ? 'check' : null,
				'text' => elgg_echo('newsletter:menu:steps:template'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'template'),
				'selected' => $current_step === 'template',
			]);
			
			// content
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'content',
				'icon' => $entity->content ? 'check' : null,
				'text' => elgg_echo('newsletter:menu:steps:content'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'content'),
				'selected' => $current_step === 'content',
			]);
			
			// recipients
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'recipients',
				'icon' => $entity->getRecipients() ? 'check' : null,
				'text' => elgg_echo('newsletter:menu:steps:recipients'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'recipients'),
				'selected' => $current_step === 'recipients',
			]);
			
			// schedule
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'schedule',
				'icon' => $entity->scheduled ? 'check' : null,
				'text' => elgg_echo('newsletter:menu:steps:schedule'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'schedule'),
				'selected' => $current_step === 'schedule',
			]);
		} else {
			// basic info
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'basic',
				'text' => elgg_echo('newsletter:menu:steps:entity'),
				'href' => false,
				'selected' => true,
			]);
			
			// template
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'template',
				'text' => elgg_echo('newsletter:menu:steps:template'),
				'href' => false,
			]);
			
			// content
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'content',
				'text' => elgg_echo('newsletter:menu:steps:content'),
				'href' => false,
			]);
			
			// recipients
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'recipients',
				'text' => elgg_echo('newsletter:menu:steps:recipients'),
				'href' => false,
			]);
			
			// schedule
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'schedule',
				'text' => elgg_echo('newsletter:menu:steps:schedule'),
				'href' => false,
			]);
		}
		
		return $returnvalue;
	}
}
