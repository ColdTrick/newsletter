<?php
/**
 * All event handler functions can be found in this file
 */

/**
 * Do something on the 'upgrade', 'system' event (when running upgrade.php)
 *
 * @param string $event  which event was triggered
 * @param string $type   what is the type of the event
 * @param mixed  $object On what object was the event triggered
 *
 * @return void
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
	
	// proccess upgrade scripts
	$upgrade_scripts = array();
	$upgrade_dir = dirname(__FILE__) . "/upgrades/";
	$fh = opendir($upgrade_dir);
	
	// read all available upgrade scripts
	if (!empty($fh)) {
		while (($upgrade_file = readdir($fh)) !== false) {
			if (!is_dir($upgrade_dir . $upgrade_file)) {
				$upgrade_scripts[] = $upgrade_file;
			}
		}
		
		closedir($fh);
	}
	
	if (!empty($upgrade_scripts)) {
		// get already run scripts
		$upgrades = datalist_get("processed_upgrades");
		$processed_upgrades = unserialize($upgrades);
		if (!is_array($processed_upgrades)) {
			$processed_upgrades = array();
		}
		
		// do we have something left
		$unprocessed = array_diff($upgrade_scripts, $processed_upgrades);
		if (!empty($unprocessed)) {
			// proccess all upgrades
			foreach ($unprocessed as $script) {
				include($upgrade_dir . $script);
				
				$processed_upgrades[] = $script;
			}
			
			// save new list
			elgg_set_processed_upgrades($processed_upgrades);
		}
	}
	
}

/**
 * Listen to the join site event and check for email subscriptions
 *
 * @param string           $event  What event was fired
 * @param string           $type   What was the type of event
 * @param ElggRelationship $object The relationship object, containing the user and site
 *
 * @return void
 */
function newsletter_join_site_event_handler($event, $type, $object) {
	
	if (!empty($object) && ($object instanceof ElggRelationship)) {
		$user = get_user($object->guid_one);
		
		if (!empty($user)) {
			$subscription = newsletter_get_subscription($user->email);
			
			if (!empty($subscription)) {
				newsletter_convert_subscription_to_user_setting($subscription, $user);
			}
		}
	}
}
