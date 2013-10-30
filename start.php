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
		
		// register plugin hooks
		elgg_register_plugin_hook_handler("cron", "hourly", "newsletter_cron_handler");
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