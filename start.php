<?php

	// load library files
	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/hooks.php");
	require_once(dirname(__FILE__) . "/lib/page_handlers.php");
	
	// register default Elgg events
	elgg_register_event_handler("init", "system", "newsletter_init");
	
	/**
	 * This function is called when the Elgg system gets initialized
	 */
	function newsletter_init() {
		
		// register page handler
		elgg_register_page_handler("newsletter", "newsletter_page_handler");
		
		// register plugin hooks
		elgg_register_plugin_hook_handler("cron", "hourly", "newsletter_cron_handler");
	}