<?php

	// load library files
	require_once(dirname(__FILE__) . "/lib/functions.php");
	
	// register default Elgg events
	elgg_register_event_handler("init", "system", "newsletter_init");
	
	/**
	 * This function is calle when the Elgg system gets initialized
	 */
	function newsletter_init() {
		
	}