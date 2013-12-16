<?php

	/**
	 * All event handler functions can be found in this file
	 */

	/**
	 * Do something on the 'upgrade', 'system' event (when running upgrade.php)
	 *
	 * @param 	string 	$event	which event was triggered
	 * @param 	string	$type	what is the type of the event
	 * @param 	mixed 	$object	On what object was the event triggered
	 *
	 * @see elgg_trigger_event()
	 */
	function newsletter_upgrade_event_handler($event, $type, $object) {
		
		// amke sure the correct classes are set for our own classes
		if (!update_subtype("object", Newsletter::SUBTYPE, "Newsletter")) {
			// first time the plugin was activated
			add_subtype("object", Newsletter::SUBTYPE, "Newsletter");
		}
		
		if (!update_subtype("object", NewsletterSubscription::SUBTYPE, "NewsletterSubscription")) {
			// first time the plugin was activated
			add_subtype("object", NewsletterSubscription::SUBTYPE, "NewsletterSubscription");
		}
	}