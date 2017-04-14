<?php

// some subtype defines
define('NEWSLETTER_CONTENT_SUBTYPE', 'newsletter_content');
define('NEWSLETTER_TEMPLATE', 'newsletter_template');

// load library files
require_once(dirname(__FILE__) . '/lib/functions.php');

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
	
	// Register entity_type for search
	elgg_register_entity_type('object', 'newsletter');
	
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
	elgg_register_event_handler('upgrade', 'system', '\ColdTrick\Newsletter\Upgrade::urlPostfix');
	elgg_register_event_handler('create', 'relationship', '\ColdTrick\Newsletter\Site::join');
	
	// register actions
	elgg_register_action('newsletter/edit', dirname(__FILE__) . '/actions/edit.php');
	elgg_register_action('newsletter/edit/schedule', dirname(__FILE__) . '/actions/edit/schedule.php');
	elgg_register_action('newsletter/edit/content', dirname(__FILE__) . '/actions/edit/content.php');
	elgg_register_action('newsletter/edit/template', dirname(__FILE__) . '/actions/edit/template.php');
	elgg_register_action('newsletter/edit/template_to_custom', dirname(__FILE__) . '/actions/edit/template_to_custom.php');
	elgg_register_action('newsletter/edit/recipients', dirname(__FILE__) . '/actions/edit/recipients.php');
	
	elgg_register_action('newsletter/delete', dirname(__FILE__) . '/actions/delete.php');
	elgg_register_action('newsletter/send', dirname(__FILE__) . '/actions/send.php');
	elgg_register_action('newsletter/subscribe', dirname(__FILE__) . '/actions/subscribe.php', 'public');
	elgg_register_action('newsletter/unsubscribe', dirname(__FILE__) . '/actions/unsubscribe.php', 'public');
	elgg_register_action('newsletter/subscriptions', dirname(__FILE__) . '/actions/subscriptions.php');
	elgg_register_action('newsletter/duplicate', dirname(__FILE__) . '/actions/duplicate.php');
	elgg_register_action('newsletter/preview_mail', dirname(__FILE__) . '/actions/preview_mail.php');
	
	elgg_register_action('newsletter/template/edit', dirname(__FILE__) . '/actions/template/edit.php');
	elgg_register_action('newsletter/template/delete', dirname(__FILE__) . '/actions/template/delete.php');
}
