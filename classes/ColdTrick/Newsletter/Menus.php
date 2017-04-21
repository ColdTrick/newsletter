<?php

namespace ColdTrick\Newsletter;

class Menus {
	
	/**
	 * Add a menu item in the owner block menu of a group
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue Default menu items
	 * @param array  $params      params for the hook
	 *
	 * @return array Menu items
	 */
	public static function ownerBlockRegister($hook, $type, $returnvalue, $params) {

		$entity = elgg_extract('entity', $params);
		if (!elgg_instanceof($entity, 'group')) {
			return;
		}
		
		if (!newsletter_is_group_enabled($entity)) {
			return;
		}
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter',
			'text' => elgg_echo('newsletter:menu:owner_block:group'),
			'href' => 'newsletter/group/' . $entity->getGUID(),
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
	 * @return array Menu items
	 */
	public static function filterRegister($hook, $type, $returnvalue, $params) {
		
		if (!elgg_in_context('newsletter')) {
			return;
		}

		$page_owner = elgg_get_page_owner_entity();
		if (empty($page_owner) && !elgg_is_admin_logged_in()) {
			// site newsletters
			return [];
		}
		
		if (!empty($page_owner) && !$page_owner->canEdit()) {
			// group newsletters
			return [];
		}
			
		$returnvalue = [];
		
		$base_url = 'newsletter/site';
		if (elgg_instanceof($page_owner, 'group')) {
			$base_url = 'newsletter/group/' . $page_owner->getGUID();
		}
		$current_filter = get_input('filter', 'sent');
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'concept',
			'text' => elgg_echo('newsletter:menu:filter:concept'),
			'href' => $base_url . '?filter=concept',
			'is_trusted' => true,
			'selected' => ($current_filter == 'concept'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'scheduled',
			'text' => elgg_echo('newsletter:menu:filter:scheduled'),
			'href' => $base_url . '?filter=scheduled',
			'is_trusted' => true,
			'selected' => ($current_filter == 'scheduled'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sending',
			'text' => elgg_echo('newsletter:menu:filter:sending'),
			'href' => $base_url . '?filter=sending',
			'is_trusted' => true,
			'selected' => ($current_filter == 'sending'),
		]);
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'sent',
			'text' => elgg_echo('newsletter:menu:filter:sent'),
			'href' => $base_url . '?filter=sent',
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
	 * @return array Menu items
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
			'href' => "newsletter/embed/{$guid}",
			'text' => elgg_echo('newsletter:menu:longtext:embed_content'),
			'link_class' => 'elgg-longtext-control elgg-lightbox',
			'priority' => 5,
		]);

		elgg_load_js('lightbox');
		elgg_load_css('lightbox');
		elgg_require_js('newsletter/embed');
	
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
	 * @return array Menu items
	 */
	public static function newsletterButtonsRegister($hook, $type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!elgg_instanceof($entity, 'object', \Newsletter::SUBTYPE)) {
			return;
		}
		
		$container = $entity->getContainerEntity();
		if ($container instanceof \ElggGroup) {
			$href = 'newsletter/group/' . $container->getGUID();
		} else {
			$href = 'newsletter/site';
		}
			
		$referer = elgg_extract('HTTP_REFERER', $_SERVER);
		if (!empty($referer) && stristr($referer, elgg_get_site_url())) {
			// there is history to this site, so add a back button
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'back',
				'href' => $referer,
				'text' => elgg_echo('back'),
				'target' => '_self',
			]);
		}
			
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'more',
			'href' => $href,
			'text' => elgg_echo('newsletter:menu:site'),
			'target' => '_self',
		]);
			
		if ($entity->canEdit()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'edit',
				'href' => 'newsletter/edit/' . $entity->getGUID(),
				'text' => elgg_echo('edit'),
				'target' => '_self',
			]);

			if (elgg_extract('type', $params) == 'preview') {
				$returnvalue[] = \ElggMenuItem::factory([
					'name' => 'mail',
					'href' => false,
					'text' => elgg_view_form('newsletter/preview_mail', ['target' => '_self'], ['entity' => $entity]),
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
	 * @return array Menu items
	 */
	public static function entityRegister($hook, $type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!elgg_instanceof($entity, 'object', \Newsletter::SUBTYPE)) {
			return;
		}
		
		if (!$entity->canEdit()) {
			return;
		}

		if (($entity->status == 'sent') || $entity->getLogging()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'log',
				'href' => 'newsletter/log/' . $entity->getGUID(),
				'text' => elgg_echo('newsletter:menu:entity:log'),
			]);
		}

		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'duplicate',
			'href' => 'action/newsletter/duplicate?guid=' . $entity->getGUID(),
			'text' => elgg_echo('newsletter:menu:entity:duplicate'),
			'is_action' => true,
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
	 * @return array Menu items
	 */
	public static function newsletterSteps($hook, $type, $returnvalue, $params) {
	
		$entity = elgg_extract('entity', $params);
	
		if (elgg_instanceof($entity, 'object', \Newsletter::SUBTYPE)) {
			// basic info
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'basic',
				'href' => 'newsletter/edit/' . $entity->getGUID(),
				'text' => elgg_view_icon('checkmark', 'mrs') . elgg_echo('newsletter:menu:steps:entity'),
			]);
	
			// template
			$item = \ElggMenuItem::factory([
				'name' => 'template',
				'href' => 'newsletter/edit/' . $entity->getGUID() . '/template',
				'text' => elgg_echo('newsletter:menu:steps:template'),
			]);
	
			if ($entity->template) {
				$item->setText(elgg_view_icon('checkmark', 'mrs') . $item->getText());
			}
	
			$returnvalue[] = $item;
	
			// content
			$item = \ElggMenuItem::factory([
				'name' => 'content',
				'href' => 'newsletter/edit/' . $entity->getGUID() . '/content',
				'text' => elgg_echo('newsletter:menu:steps:content'),
			]);
	
			if ($entity->content) {
				$item->setText(elgg_view_icon('checkmark', 'mrs') . $item->getText());
			}
	
			$returnvalue[] = $item;
	
			// recipients
			$item = \ElggMenuItem::factory([
				'name' => 'recipients',
				'href' => 'newsletter/edit/' . $entity->getGUID() . '/recipients',
				'text' => elgg_echo('newsletter:menu:steps:recipients'),
			]);
	
			if ($entity->getRecipients()) {
				$item->setText(elgg_view_icon('checkmark', 'mrs') . $item->getText());
			}
				
			$returnvalue[] = $item;
				
			// schedule
			$item = \ElggMenuItem::factory([
				'name' => 'schedule',
				'href' => 'newsletter/edit/' . $entity->getGUID() . '/schedule',
				'text' => elgg_echo('newsletter:menu:steps:schedule'),
			]);
	
			if ($entity->scheduled) {
				$item->setText(elgg_view_icon('checkmark', 'mrs') . $item->getText());
			}
	
			$returnvalue[] = $item;
		} else {
			// basic info
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'basic',
				'href' => 'javascript:void(0);',
				'text' => elgg_echo('newsletter:menu:steps:entity'),
				'selected' => true,
			]);
	
			// template
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'template',
				'href' => 'javascript:void(0);',
				'text' => elgg_echo('newsletter:menu:steps:template'),
			]);
	
			// content
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'content',
				'href' => 'javascript:void(0);',
				'text' => elgg_echo('newsletter:menu:steps:content'),
			]);
			// recipients
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'recipients',
				'href' => 'javascript:void(0);',
				'text' => elgg_echo('newsletter:menu:steps:recipients'),
			]);
	
			// schedule
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'schedule',
				'href' => 'javascript:void(0);',
				'text' => elgg_echo('newsletter:menu:steps:schedule'),
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
	 * @return array Menu items
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
				'href' => "newsletter/subscriptions/{$user->username}",
				'text' => elgg_echo('newsletter:menu:page:subscriptions'),
				'is_trusted' => true,
			]);
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'newsletter_received',
				'href' => "newsletter/received/{$user->username}",
				'text' => elgg_echo('newsletter:menu:page:received'),
				'is_trusted' => true,
			]);
		}
	
		// settings pages
		$page_owner = elgg_get_page_owner_entity();
		if (elgg_in_context('settings') && elgg_instanceof($page_owner, 'user')) {
			if ($page_owner->canEdit()) {
				$returnvalue[] = \ElggMenuItem::factory([
					'name' => 'edit',
					'href' => "newsletter/subscriptions/{$page_owner->username}",
					'text' => elgg_echo('newsletter:menu:page:settings'),
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
	 * @return array Menu items
	 */
	public static function siteRegister($hook, $type, $returnvalue, $params) {

		// link to your subscriptions
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter',
			'href' => 'newsletter/site',
			'text' => elgg_echo('newsletter:menu:site'),
			'is_trusted' => true,
		]);

		return $returnvalue;
	}
}