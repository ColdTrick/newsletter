<?php

	// some subtype defines
	define("NEWSLETTER_CONTENT_SUBTYPE", "newsletter_content");
	define("NEWSLETTER_TEMPLATE", "newsletter_template");

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
		
		// register plugin hooks
		elgg_register_plugin_hook_handler("cron", "hourly", "newsletter_cron_handler");
		elgg_register_plugin_hook_handler("access:collections:write", "user", "newsletter_write_access_handler");
		
		// register actions
		elgg_register_action("newsletter/edit", dirname(__FILE__) . "/actions/edit.php");
		elgg_register_action("newsletter/delete", dirname(__FILE__) . "/actions/delete.php");
		elgg_register_action("newsletter/edit/schedule", dirname(__FILE__) . "/actions/edit/schedule.php");
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
