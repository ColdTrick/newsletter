<?php

// some subtype defines
define("NEWSLETTER_CONTENT_SUBTYPE", "newsletter_content");
define("NEWSLETTER_TEMPLATE", "newsletter_template");

// load library files
require_once(dirname(__FILE__) . "/lib/functions.php");
require_once(dirname(__FILE__) . "/lib/events.php");
require_once(dirname(__FILE__) . "/lib/hooks.php");
require_once(dirname(__FILE__) . "/lib/page_handlers.php");

// register default Elgg events
elgg_register_event_handler("init", "system", "newsletter_init");
elgg_register_event_handler("pagesetup", "system", "newsletter_pagesetup");

/**
 * This function is called when the Elgg system gets initialized
 *
 * @return void
 */
function newsletter_init() {
	
	// register page handler
	elgg_register_page_handler("newsletter", "newsletter_page_handler");
	
	// Register entity_type for search
	elgg_register_entity_type("object", "newsletter");
	
	// CSS & JS
	elgg_extend_view("css/elgg", "css/newsletter/site");
	
	elgg_register_simplecache_view("js/newsletter/embed.js");
	
	// extend views
	elgg_extend_view("groups/sidebar/my_status", "newsletter/sidebar/subscribe");
	elgg_extend_view("register/extend", "newsletter/register");
	
	// create group setting
	if (newsletter_is_group_enabled()) {
		add_group_tool_option("newsletter", elgg_echo("newsletter:group:tool_option"), true);
	}
	
	// widget
	elgg_register_widget_type("newsletter_subscribe", elgg_echo("newsletter:sidebar:subscribe:title"), elgg_echo("newsletter:widget:subscribe:description"), array("index","groups"));
	
	// register plugin hooks
	elgg_register_plugin_hook_handler("cron", "hourly", "newsletter_cron_handler");
	elgg_register_plugin_hook_handler("access:collections:write", "all", "newsletter_write_access_handler", 600); // needs to be after groups
	
	elgg_register_plugin_hook_handler("register", "menu:page", "newsletter_register_page_menu_handler");
	elgg_register_plugin_hook_handler("register", "menu:newsletter_steps", "newsletter_register_newsletter_steps_menu_handler");
	elgg_register_plugin_hook_handler("register", "menu:entity", "newsletter_register_entity_menu_handler");
	elgg_register_plugin_hook_handler("register", "menu:owner_block", "newsletter_register_owner_block_menu_handler");
	elgg_register_plugin_hook_handler("register", "menu:filter", "newsletter_register_filter_menu_handler");
	elgg_register_plugin_hook_handler("register", "menu:longtext", "newsletter_register_longtext_menu_handler");
	elgg_register_plugin_hook_handler("register", "menu:newsletter_buttons", "newsletter_register_buttons_menu_handler");
	elgg_register_plugin_hook_handler("register", "menu:longtext", "newsletter_register_longtext_menu_handler");
	
	elgg_register_plugin_hook_handler("usersettings:save", "user", "newsletter_usersettings_save_handler");
	elgg_register_plugin_hook_handler("register", "user", "newsletter_register_user_handler");
	elgg_register_plugin_hook_handler("entity:icon:url", "object", "newsletter_icon_hook");
	
	elgg_register_plugin_hook_handler("widget_url", "widget_manager", "newsletter_widget_url_handler");
	elgg_register_plugin_hook_handler("notification_type_subtype", "tag_tools", "newsletter_tag_tools_notifications");
	
	// extend public pages
	elgg_register_plugin_hook_handler('public_pages', 'walled_garden', 'newsletter_public_pages');
	
	// register event handlers
	elgg_register_event_handler("upgrade", "system", "newsletter_upgrade_event_handler");
	elgg_register_event_handler("create", "member_of_site", "newsletter_join_site_event_handler");
	
	// register actions
	elgg_register_action("newsletter/edit", dirname(__FILE__) . "/actions/edit.php");
	elgg_register_action("newsletter/edit/schedule", dirname(__FILE__) . "/actions/edit/schedule.php");
	elgg_register_action("newsletter/edit/content", dirname(__FILE__) . "/actions/edit/content.php");
	elgg_register_action("newsletter/edit/template", dirname(__FILE__) . "/actions/edit/template.php");
	elgg_register_action("newsletter/edit/template_to_custom", dirname(__FILE__) . "/actions/edit/template_to_custom.php");
	elgg_register_action("newsletter/edit/recipients", dirname(__FILE__) . "/actions/edit/recipients.php");
	
	elgg_register_action("newsletter/delete", dirname(__FILE__) . "/actions/delete.php");
	elgg_register_action("newsletter/send", dirname(__FILE__) . "/actions/send.php");
	elgg_register_action("newsletter/subscribe", dirname(__FILE__) . "/actions/subscribe.php", "public");
	elgg_register_action("newsletter/unsubscribe", dirname(__FILE__) . "/actions/unsubscribe.php", "public");
	elgg_register_action("newsletter/subscriptions", dirname(__FILE__) . "/actions/subscriptions.php");
	elgg_register_action("newsletter/duplicate", dirname(__FILE__) . "/actions/duplicate.php");
	elgg_register_action("newsletter/preview_mail", dirname(__FILE__) . "/actions/preview_mail.php");
	
	elgg_register_action("newsletter/template/edit", dirname(__FILE__) . "/actions/template/edit.php");
	elgg_register_action("newsletter/template/delete", dirname(__FILE__) . "/actions/template/delete.php");
}


/**
 * Page setup for newsletter
 *
 * @return void
 */
function newsletter_pagesetup() {
	// register site menu
	elgg_register_menu_item("site", array(
		"name" => "newsletter",
		"text" => elgg_echo("newsletter:menu:site"),
		"href" => "newsletter/site",
		"is_trusted" => true
	));
}
