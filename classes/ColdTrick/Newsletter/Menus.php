<?php

namespace ColdTrick\Newsletter;

use Elgg\Menu\MenuItems;

/**
 * Menu callbacks
 */
class Menus {
	
	/**
	 * Add a menu item in the owner block menu of a group
	 *
	 * @param \Elgg\Event $event 'register', 'menu:owner_block'
	 *
	 * @return MenuItems
	 */
	public static function ownerBlockRegister(\Elgg\Event $event) {

		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggGroup) {
			return;
		}
		
		if (!newsletter_is_group_enabled($entity)) {
			return;
		}
		
		$returnvalue = $event->getValue();
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter',
			'text' => elgg_echo('newsletter:menu:owner_block:group'),
			'href' => elgg_generate_url('collection:object:newsletter:group', [
				'guid' => $entity->guid,
			]),
			'is_trusted' => true,
		]);
		
		return $returnvalue;
	}
	
	/**
	 * Replace the filter menu on the newsletter pages
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:newsletter[/group]'
	 *
	 * @return MenuItems
	 */
	public static function filterRegister(\Elgg\Event $event) {
		
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup && !$page_owner->canEdit()) {
			return;
		}
		
		if (empty($page_owner) && !elgg_is_admin_logged_in()) {
			return;
		}
		
		$route_name = 'collection:object:newsletter:site';
		$route_params = [];
		if ($page_owner instanceof \ElggGroup) {
			$route_name = 'collection:object:newsletter:group';
			$route_params['guid'] = $page_owner->guid;
		}
		
		$current_filter = $event->getParam('filter_value', 'sent');
		
		$returnvalue = $event->getValue();
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'concept',
			'text' => elgg_echo('newsletter:menu:filter:concept'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'concept',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'concept'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'scheduled',
			'text' => elgg_echo('newsletter:menu:filter:scheduled'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'scheduled',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'scheduled'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sending',
			'text' => elgg_echo('newsletter:menu:filter:sending'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'sending',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'sending'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sent',
			'text' => elgg_echo('newsletter:menu:filter:sent'),
			'href' => elgg_generate_url($route_name, $route_params + [
				'filter' => 'sent',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'sent'),
		]);
	
		return $returnvalue;
	}
		
	/**
	 * Add a menu item in the entity's menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return MenuItems
	 */
	public static function entityRegister(\Elgg\Event $event) {
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \Newsletter || !$entity->canEdit()) {
			return;
		}

		$returnvalue = $event->getValue();
		if (($entity->status == 'sent') || $entity->getLogging()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'log',
				'icon' => 'file-alt',
				'text' => elgg_echo('newsletter:menu:entity:log'),
				'href' => elgg_generate_entity_url($entity, 'log'),
			]);
		}

		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'duplicate',
			'icon' => 'copy',
			'text' => elgg_echo('newsletter:menu:entity:duplicate'),
			'href' => elgg_generate_action_url('newsletter/duplicate', [
				'guid' => $entity->guid,
			]),
			'is_trusted' => true,
		]);
	
		return $returnvalue;
	}

	/**
	 * Add a menu item in the sidebar for the steps of creating a newsletter
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:newsletter_steps'
	 *
	 * @return MenuItems
	 */
	public static function newsletterSteps(\Elgg\Event $event) {
		
		$entity = $event->getParam('filter_entity', $event->getEntityParam());
		
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
			$item = \ElggMenuItem::factory([
				'name' => 'template',
				'text' => elgg_echo('newsletter:menu:steps:template'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'template'),
				'selected' => $current_step === 'template',
			]);
			
			if ($entity->template) {
				$item->icon = 'check';
			}
			
			$returnvalue[] = $item;
			
			// content
			$item = \ElggMenuItem::factory([
				'name' => 'content',
				'text' => elgg_echo('newsletter:menu:steps:content'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'content'),
				'selected' => $current_step === 'content',
			]);
			
			if ($entity->content) {
				$item->icon = 'check';
			}
			
			$returnvalue[] = $item;
			
			// recipients
			$item = \ElggMenuItem::factory([
				'name' => 'recipients',
				'text' => elgg_echo('newsletter:menu:steps:recipients'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'recipients'),
				'selected' => $current_step === 'recipients',
			]);
			
			if ($entity->getRecipients()) {
				$item->icon = 'check';
			}
			
			$returnvalue[] = $item;
			
			// schedule
			$item = \ElggMenuItem::factory([
				'name' => 'schedule',
				'text' => elgg_echo('newsletter:menu:steps:schedule'),
				'href' => elgg_generate_entity_url($entity, 'edit', 'schedule'),
				'selected' => $current_step === 'schedule',
			]);
			
			if ($entity->scheduled) {
				$item->icon = 'check';
			}
			
			$returnvalue[] = $item;
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
	
	/**
	 * Add a menu item in the sidebar to go to the newsletter subsciptions
	 *
	 * @param \Elgg\Event $event 'register', 'menu:page'
	 *
	 * @return MenuItems
	 */
	public static function pageRegister(\Elgg\Event $event) {

		$user = elgg_get_logged_in_user_entity();
		if (empty($user)) {
			return;
		}
		
		$returnvalue = $event->getValue();
		if (elgg_in_context('newsletter') && !elgg_in_context('settings')) {
			// link to your subscriptions
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'newsletter_suscriptions',
				'text' => elgg_echo('newsletter:menu:page:subscriptions'),
				'href' => elgg_generate_url('collection:object:newsletter:subscriptions', [
					'username' => $user->username,
				]),
				'is_trusted' => true,
			]);
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'newsletter_received',
				'href' => elgg_generate_url('collection:object:newsletter:received', [
					'username' => $user->username,
				]),
				'text' => elgg_echo('newsletter:menu:page:received'),
				'is_trusted' => true,
			]);
		}
		
		// settings pages
		$page_owner = elgg_get_page_owner_entity();
		if (elgg_in_context('settings') && $page_owner instanceof \ElggUser) {
			if ($page_owner->canEdit()) {
				$returnvalue[] = \ElggMenuItem::factory([
					'name' => 'edit',
					'text' => elgg_echo('newsletter:menu:page:settings'),
					'href' => elgg_generate_url('collection:object:newsletter:subscriptions', [
						'username' => $page_owner->username,
					]),
					'section' => 'notifications',
					'is_trusted' => true,
				]);
			}
		}
	
		return $returnvalue;
	}
	
	/**
	 * Registers menu items to the site menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:site'
	 *
	 * @return MenuItems
	 */
	public static function siteRegister(\Elgg\Event $event) {
		$returnvalue = $event->getValue();
		
		// link to your subscriptions
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter',
			'icon' => 'mail-bulk',
			'text' => elgg_echo('newsletter:menu:site'),
			'href' => elgg_generate_url('collection:object:newsletter:site'),
			'is_trusted' => true,
		]);

		return $returnvalue;
	}
}
