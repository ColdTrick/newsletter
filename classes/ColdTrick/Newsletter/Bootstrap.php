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
		$this->registerHooks();
	}
	
	/**
	 * Extend views
	 *
	 * @return void
	 */
	protected function extendViews() {
		
		elgg_extend_view('css/elgg', 'css/newsletter.css');
		elgg_extend_view('register/extend', 'newsletter/register');
		
		elgg_register_ajax_view('forms/newsletter/preview_mail');
		elgg_register_ajax_view('forms/newsletter/subscribe');
	}
	
	/**
	 * Register plugin hook handles
	 *
	 * @return void
	 */
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('access:collections:write', 'all', __NAMESPACE__ . '\Access::writeAccessCollections', 700); // needs to be after groups
		$hooks->registerHandler('container_logic_check', 'object', __NAMESPACE__ . '\Permissions::containerLogic');
		$hooks->registerHandler('cron', 'hourly', __NAMESPACE__ . '\Cron::sendNewsletters');
		$hooks->registerHandler('entity:icon:sizes', 'object', __NAMESPACE__ . '\Icons::extendIconSizes');
		$hooks->registerHandler('export_value', 'csv_exporter', __NAMESPACE__ . '\Plugins\CSVExporter::exportValue');
		$hooks->registerHandler('get_exportable_values', 'csv_exporter', __NAMESPACE__ . '\Plugins\CSVExporter::exportableValues');
		$hooks->registerHandler('likes:is_likable', 'object:' . \Newsletter::SUBTYPE, '\Elgg\Values::getTrue');
		$hooks->registerHandler('notification_type_subtype', 'tag_tools', __NAMESPACE__ . '\Plugins\TagTools::notificationTypeSubtype');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\Menus::pageRegister');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\Menus::entityRegister');
		$hooks->registerHandler('register', 'menu:owner_block', __NAMESPACE__ . '\Menus::ownerBlockRegister');
		$hooks->registerHandler('register', 'menu:filter:newsletter', __NAMESPACE__ . '\Menus::filterRegister');
		$hooks->registerHandler('register', 'menu:filter:newsletter/group', __NAMESPACE__ . '\Menus::filterRegister');
		$hooks->registerHandler('register', 'menu:filter:newsletter_steps', __NAMESPACE__ . '\Menus::newsletterSteps');
		$hooks->registerHandler('register', 'menu:longtext', __NAMESPACE__ . '\Menus::longtextRegister');
		$hooks->registerHandler('register', 'menu:site', __NAMESPACE__ . '\Menus::siteRegister');
		$hooks->registerHandler('register', 'user', __NAMESPACE__ . '\User::subscribeToSiteNewsletter');
		$hooks->registerHandler('usersettings:save', 'user', __NAMESPACE__ . '\User::convertEmailSubscriptionToUserSetting');
		$hooks->registerHandler('entity:url', 'object', __NAMESPACE__ . '\Widgets::widgetURL');
		$hooks->registerHandler('view', 'page/layouts/newsletter', __NAMESPACE__ . '\Plugins\DeveloperTools::reenableLogOutput');
		$hooks->registerHandler('view_vars', 'page/layouts/newsletter', __NAMESPACE__ . '\Plugins\DeveloperTools::preventLogOutput');
	}
}
