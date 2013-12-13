<?php

	// some subtype defines
	define("NEWSLETTER_CONTENT_SUBTYPE", "newsletter_content");
	define("NEWSLETTER_TEMPLATE", "newsletter_template");
	define("NEWSLETTER_USER_SUBSCRIPTION", "subscribed");
	define("NEWSLETTER_USER_BLACKLIST", "blocked");

	// load library files
	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/hooks.php");
	require_once(dirname(__FILE__) . "/lib/page_handlers.php");
	
	// register default Elgg events
	elgg_register_event_handler("init", "system", "newsletter_init");
	elgg_register_event_handler("pagesetup", "system", "newsletter_pagesetup");
	
	/**
	 * This function is called when the Elgg system gets initialized
	 */
	function newsletter_init() {
		
		// register page handler
		elgg_register_page_handler("newsletter", "newsletter_page_handler");
		
		// views
		elgg_extend_view("css/elgg", "css/newsletter/site");
		elgg_extend_view("js/elgg", "js/newsletter/site");
		
		// register JS library
		elgg_register_simplecache_view("js/newsletter/recipients");
		$url = elgg_get_simplecache_url("js", "newsletter/recipients");
		elgg_register_js("newsletter.recipients", $url);
		
		// extend the group profile sidebar
		elgg_extend_view("groups/sidebar/my_status", "newsletter/sidebar/subscribe");
		
		// register plugin hooks
		elgg_register_plugin_hook_handler("cron", "hourly", "newsletter_cron_handler");
		elgg_register_plugin_hook_handler("access:collections:write", "user", "newsletter_write_access_handler");
		elgg_register_plugin_hook_handler("register", "menu:page", "newsletter_register_page_menu_handler");
		
		// register actions
		elgg_register_action("newsletter/edit", dirname(__FILE__) . "/actions/edit.php");
		elgg_register_action("newsletter/edit/schedule", dirname(__FILE__) . "/actions/edit/schedule.php");
		elgg_register_action("newsletter/edit/content", dirname(__FILE__) . "/actions/edit/content.php");
		elgg_register_action("newsletter/edit/styling", dirname(__FILE__) . "/actions/edit/styling.php");
		elgg_register_action("newsletter/edit/recipients", dirname(__FILE__) . "/actions/edit/recipients.php");
		
		elgg_register_action("newsletter/delete", dirname(__FILE__) . "/actions/delete.php");
		elgg_register_action("newsletter/subscribe", dirname(__FILE__) . "/actions/subscribe.php", "public");
		elgg_register_action("newsletter/subscriptions", dirname(__FILE__) . "/actions/subscriptions.php");
	}
	
	function newsletter_pagesetup() {
		// register site menu
		elgg_register_menu_item("site", array(
			"name" => "newsletter",
			"text" => elgg_echo("newsletter:menu:site"),
			"href" => "newsletter/site",
			"is_trusted" => true
		));
	}
