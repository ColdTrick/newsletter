<?php

namespace ColdTrick\Newsletter;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\DefaultPluginBootstrap::init()
	 */
	public function init() {
		
		// create group setting
		if (newsletter_is_group_enabled()) {
			$this->elgg()->group_tools->register('newsletter', [
				'label' => elgg_echo('newsletter:group:tool_option'),
				'default_on' => true,
			]);
		}
		
		$this->extendViews();
		$this->registerEvents();
		$this->registerHooks();
	}
	
	/**
	 * Extend views
	 *
	 * @return void
	 */
	protected function extendViews() {
		
		elgg_extend_view('css/elgg', 'css/newsletter.css');
		elgg_extend_view('groups/sidebar/my_status', 'newsletter/sidebar/subscribe');
		elgg_extend_view('register/extend', 'newsletter/register');
		
		elgg_register_simplecache_view('js/newsletter/embed.js');
	}
	
	/**
	 * Register plugin hook handles
	 *
	 * @return void
	 */
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('access:collections:write', 'all', __NAMESPACE__ . '\Access::writeAccessCollections', 600); // needs to be after groups
		$hooks->registerHandler('cron', 'hourly', __NAMESPACE__ . '\Cron::sendNewsletters');
		$hooks->registerHandler('likes:is_likable', 'object:' . \Newsletter::SUBTYPE, '\Elgg\Values::getTrue');
		$hooks->registerHandler('notification_type_subtype', 'tag_tools', __NAMESPACE__ . '\TagTools::notificationTypeSubtype');
		$hooks->registerHandler('public_pages', 'walled_garden', __NAMESPACE__ . '\Site::publicPages');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\Menus::pageRegister');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\Menus::entityRegister');
		$hooks->registerHandler('register', 'menu:owner_block', __NAMESPACE__ . '\Menus::ownerBlockRegister');
		$hooks->registerHandler('register', 'menu:filter:newsletter', __NAMESPACE__ . '\Menus::filterRegister');
		$hooks->registerHandler('register', 'menu:filter:newsletter_steps', __NAMESPACE__ . '\Menus::newsletterSteps');
		$hooks->registerHandler('register', 'menu:newsletter_buttons', __NAMESPACE__ . '\Menus::newsletterButtonsRegister');
		$hooks->registerHandler('register', 'menu:longtext', __NAMESPACE__ . '\Menus::longtextRegister');
		$hooks->registerHandler('register', 'menu:site', __NAMESPACE__ . '\Menus::siteRegister');
		$hooks->registerHandler('register', 'user', __NAMESPACE__ . '\User::subscribeToSiteNewsletter');
		$hooks->registerHandler('usersettings:save', 'user', __NAMESPACE__ . '\User::convertEmailSubscriptionToUserSetting');
		$hooks->registerHandler('entity:url', 'object', __NAMESPACE__ . '\Widgets::widgetURL');
	}
	
	/**
	 * Register event handlers
	 *
	 * @return void
	 */
	protected function registerEvents() {
		$events = $this->elgg()->events;
		
		$events->registerHandler('create', 'relationship', __NAMESPACE__ . '\Site::join');
	}
}
