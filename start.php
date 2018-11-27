<?php

// register default Elgg events
elgg_register_event_handler('init', 'system', 'newsletter_init');

/**
 * This function is called when the Elgg system gets initialized
 *
 * @return void
 */
function newsletter_init() {
	
	// register page handler
	elgg_register_page_handler('newsletter', '\ColdTrick\Newsletter\PageHandler::newsletter');
	
	// CSS & JS
	elgg_extend_view('css/elgg', 'css/newsletter.css');
	
	elgg_register_simplecache_view('js/newsletter/embed.js');
	
	// extend views
	elgg_extend_view('groups/sidebar/my_status', 'newsletter/sidebar/subscribe');
	elgg_extend_view('register/extend', 'newsletter/register');
	
	// create group setting
	if (newsletter_is_group_enabled()) {
		add_group_tool_option('newsletter', elgg_echo('newsletter:group:tool_option'), true);
	}
	
	// widget
	elgg_register_widget_type('newsletter_subscribe', elgg_echo('newsletter:sidebar:subscribe:title'), elgg_echo('newsletter:widget:subscribe:description'), ['index','groups']);
	
	// register plugin hooks
	elgg_register_plugin_hook_handler('cron', 'hourly', '\ColdTrick\Newsletter\Cron::sendNewsletters');
	elgg_register_plugin_hook_handler('access:collections:write', 'all', '\ColdTrick\Newsletter\Access::writeAccessCollections', 600); // needs to be after groups
	
	elgg_register_plugin_hook_handler('register', 'menu:page', '\ColdTrick\Newsletter\Menus::pageRegister');
	elgg_register_plugin_hook_handler('register', 'menu:newsletter_steps', '\ColdTrick\Newsletter\Menus::newsletterSteps');
	elgg_register_plugin_hook_handler('register', 'menu:entity', '\ColdTrick\Newsletter\Menus::entityRegister');
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', '\ColdTrick\Newsletter\Menus::ownerBlockRegister');
	elgg_register_plugin_hook_handler('register', 'menu:filter', '\ColdTrick\Newsletter\Menus::filterRegister');
	elgg_register_plugin_hook_handler('register', 'menu:newsletter_buttons', '\ColdTrick\Newsletter\Menus::newsletterButtonsRegister');
	elgg_register_plugin_hook_handler('register', 'menu:longtext', '\ColdTrick\Newsletter\Menus::longtextRegister');
	elgg_register_plugin_hook_handler('register', 'menu:site', '\ColdTrick\Newsletter\Menus::siteRegister');
	
	elgg_register_plugin_hook_handler('usersettings:save', 'user', '\ColdTrick\Newsletter\User::convertEmailSubscriptionToUserSetting');
	elgg_register_plugin_hook_handler('register', 'user', '\ColdTrick\Newsletter\User::subscribeToSiteNewsletter');
	
	elgg_register_plugin_hook_handler('widget_url', 'widget_manager', '\ColdTrick\Newsletter\Widgets::widgetURL');
	elgg_register_plugin_hook_handler('notification_type_subtype', 'tag_tools', '\ColdTrick\Newsletter\TagTools::notificationTypeSubtype');
	
	elgg_register_plugin_hook_handler('likes:is_likable', 'object:' . \Newsletter::SUBTYPE, '\Elgg\Values::getTrue');
	
	// extend public pages
	elgg_register_plugin_hook_handler('public_pages', 'walled_garden', '\ColdTrick\Newsletter\Site::publicPages');
	
	// register event handlers
	elgg_register_event_handler('create', 'relationship', '\ColdTrick\Newsletter\Site::join');
}
