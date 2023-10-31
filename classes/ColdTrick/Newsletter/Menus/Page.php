<?php

namespace ColdTrick\Newsletter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the page menu
 */
class Page {
	
	/**
	 * Add menu items to the page menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:page'
	 *
	 * @return null|MenuItems
	 */
	public static function registerSubscriptions(\Elgg\Event $event): ?MenuItems {
		$user = elgg_get_logged_in_user_entity();
		if (empty($user)) {
			return null;
		}
		
		if (!elgg_in_context('newsletter') || elgg_in_context('settings')) {
			return null;
		}
		
		/* @var $returnvalue MenuItems */
		$returnvalue = $event->getValue();
		
		// link to your subscriptions
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter_subscriptions',
			'text' => elgg_echo('newsletter:menu:page:subscriptions'),
			'href' => elgg_generate_url('collection:object:newsletter:subscriptions', [
				'username' => $user->username,
			]),
		]);
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter_received',
			'text' => elgg_echo('newsletter:menu:page:received'),
			'href' => elgg_generate_url('collection:object:newsletter:received', [
				'username' => $user->username,
			]),
		]);
		
		return $returnvalue;
	}
	
	/**
	 * Add menu items to the page menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:page'
	 *
	 * @return null|MenuItems
	 */
	public static function registerNewsletterSettings(\Elgg\Event $event): ?MenuItems {
		if (!elgg_in_context('settings')) {
			return null;
		}
		
		$page_owner = elgg_get_page_owner_entity();
		if (!$page_owner instanceof \ElggUser || !$page_owner->canEdit()) {
			return null;
		}
		
		/* @var $returnvalue MenuItems */
		$returnvalue = $event->getValue();
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'newsletter_subscriptions',
			'text' => elgg_echo('newsletter:menu:page:settings'),
			'href' => elgg_generate_url('collection:object:newsletter:subscriptions', [
				'username' => $page_owner->username,
			]),
			'section' => 'notifications',
		]);
		
		return $returnvalue;
	}
}
