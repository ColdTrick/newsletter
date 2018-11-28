<?php

namespace ColdTrick\Newsletter;

use Elgg\Menu\MenuItems;

class Menus {
	
	/**
	 * Add a menu item in the owner block menu of a group
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function ownerBlockRegister($hook, $type, $returnvalue, $params) {

		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof \ElggGroup) {
			return;
		}
		
		if (!newsletter_is_group_enabled($entity)) {
			return;
		}
		
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
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function filterRegister($hook, $type, $returnvalue, $params) {
		
		$page_owner = elgg_get_page_owner_entity();
		
		if ($page_owner instanceof \ElggGroup && !$page_owner->canEdit()) {
			return;
		}
		
		if (empty($page_owner) && !elgg_is_admin_logged_in()) {
			return;
		}
		
		$base_url = elgg_generate_url('collection:object:newsletter:site');
		if ($page_owner instanceof \ElggGroup) {
			$base_url = elgg_generate_url('collection:object:newsletter:group', [
				'guid' => $page_owner->guid,
			]);
		}
		
		$current_filter = elgg_extract('filter_value', $params, 'sent');
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'concept',
			'text' => elgg_echo('newsletter:menu:filter:concept'),
			'href' => elgg_http_add_url_query_elements($base_url, [
				'filter' => 'concept',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'concept'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'scheduled',
			'text' => elgg_echo('newsletter:menu:filter:scheduled'),
			'href' => elgg_http_add_url_query_elements($base_url, [
				'filter' => 'scheduled',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'scheduled'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sending',
			'text' => elgg_echo('newsletter:menu:filter:sending'),
			'href' => elgg_http_add_url_query_elements($base_url, [
				'filter' => 'sending',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'sending'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sent',
			'text' => elgg_echo('newsletter:menu:filter:sent'),
			'href' => elgg_http_add_url_query_elements($base_url, [
				'filter' => 'sent',
			]),
			'is_trusted' => true,
			'selected' => ($current_filter == 'sent'),
		]);
	
		return $returnvalue;
	}
	
	/**
	 * Add a menu item in the long text inputs (like embed and tinymce)
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function longtextRegister($hook, $type, $returnvalue, $params) {
	
		$id = elgg_extract('id', $params);
		if (strpos($id, 'newsletter-edit-content-') !== 0) {
			return;
		}
		
		if (!newsletter_embed_available()) {
			return;
		}
		
		$guid = str_replace('newsletter-edit-content-', '', $id);

		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter-embed-content',
			'text' => elgg_echo('newsletter:menu:longtext:embed_content'),
			'href' => elgg_generate_url('default:object:newsletter:embed', [
				'guid' => $guid,
			]),
			'link_class' => 'elgg-longtext-control elgg-lightbox',
			'priority' => 5,
			'deps' => [
				'newsletter/embed',
			],
		]);
	
		return $returnvalue;
	}
	
	/**
	 * Add a menu item in the buttons menu of the online/preview view
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function newsletterButtonsRegister($hook, $type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof \Newsletter) {
			return;
		}
		
		$container = $entity->getContainerEntity();
		if ($container instanceof \ElggGroup) {
			$href = elgg_generate_url('collection:object:newsletter:group', [
				'guid' => $container->guid,
			]);
		} else {
			$href = elgg_generate_url('collection:object:newsletter:site');
		}
			
		$referer = elgg_extract('HTTP_REFERER', $_SERVER);
		if (!empty($referer) && stristr($referer, elgg_get_site_url())) {
			// there is history to this site, so add a back button
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'back',
				'icon' => 'arrow-left',
				'text' => elgg_echo('back'),
				'href' => $referer,
				'target' => '_self',
			]);
		}
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'more',
			'text' => elgg_echo('newsletter:menu:site'),
			'href' => $href,
			'target' => '_self',
		]);
			
		if ($entity->canEdit()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'edit',
				'icon' => 'edit',
				'text' => elgg_echo('edit'),
				'href' => elgg_generate_entity_url($entity, 'edit'),
				'target' => '_self',
			]);

			if (elgg_extract('type', $params) == 'preview') {
				$returnvalue[] = \ElggMenuItem::factory([
					'name' => 'mail',
					'text' => elgg_view_form('newsletter/preview_mail', ['target' => '_self'], ['entity' => $entity]),
					'href' => false,
				]);
			}
		}
		
		return $returnvalue;
	}
	
	/**
	 * Add a menu item in the entity's menu
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function entityRegister($hook, $type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof \Newsletter || !$entity->canEdit()) {
			return;
		}

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
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function newsletterSteps($hook, $type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		
		if ($entity instanceof \Newsletter) {
			// basic info
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'basic',
				'icon' => 'checkmark',
				'text' => elgg_echo('newsletter:menu:steps:entity'),
				'href' => elgg_generate_entity_url($entity, 'edit'),
			]);
			
			// template
			$item = \ElggMenuItem::factory([
				'name' => 'template',
				'text' => elgg_echo('newsletter:menu:steps:template'),
				'href' => elgg_generate_entity_url($entity, 'edit', null, ['subpage' => 'template']),
			]);
			
			if ($entity->template) {
				$item->icon = 'checkmark';
			}
			
			$returnvalue[] = $item;
			
			// content
			$item = \ElggMenuItem::factory([
				'name' => 'content',
				'text' => elgg_echo('newsletter:menu:steps:content'),
				'href' => elgg_generate_entity_url($entity, 'edit', null, ['subpage' => 'content']),
			]);
			
			if ($entity->content) {
				$item->icon = 'checkmark';
			}
			
			$returnvalue[] = $item;
			
			// recipients
			$item = \ElggMenuItem::factory([
				'name' => 'recipients',
				'text' => elgg_echo('newsletter:menu:steps:recipients'),
				'href' => elgg_generate_entity_url($entity, 'edit', null, ['subpage' => 'recipients']),
			]);
			
			if ($entity->getRecipients()) {
				$item->icon = 'checkmark';
			}
			
			$returnvalue[] = $item;
			
			// schedule
			$item = \ElggMenuItem::factory([
				'name' => 'schedule',
				'text' => elgg_echo('newsletter:menu:steps:schedule'),
				'href' => elgg_generate_entity_url($entity, 'edit', null, ['subpage' => 'schedule']),
			]);
			
			if ($entity->scheduled) {
				$item->icon = 'checkmark';
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
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function pageRegister($hook, $type, $returnvalue, $params) {

		$user = elgg_get_logged_in_user_entity();
		if (empty($user)) {
			return;
		}
		
		if (elgg_in_context('newsletter')) {
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
						'username' => $user->username,
					]),
					'is_trusted' => true,
				]);
			}
		}
	
		return $returnvalue;
	}
	
	/**
	 * Registers menu items to the site menu
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return MenuItems
	 */
	public static function siteRegister($hook, $type, $returnvalue, $params) {

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
