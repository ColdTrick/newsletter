<?php

	/**
	 * Start the commandline to send a newsletter
	 * This is offloaded because it could take a while and/or resources
	 *
	 * @param Newsletter $entity
	 */
	function newsletter_start_commandline_sending(Newsletter $entity) {
		
		if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			// prepare commandline settings
			$settings = array(
				"guid" => $entity->getGUID(),
				"host" => $_SERVER["HTTP_HOST"],
				"memory_limit" => ini_get("memory_limit"),
				"secret" => newsletter_generate_commanline_secret($entity->getGUID())
			);
			if(isset($_SERVER["HTTPS"])){
				$settings["https"] = $_SERVER["HTTPS"];
			}
			
			// which script to run
			$script_location = dirname(dirname(__FILE__)) . "/procedures/cli.php";
			
			// convert settings to commandline params
			$query_string = http_build_query($settings, "", " ");
			
			// start the correct commandline
			if (PHP_OS === "WINNT") {
				pclose(popen("start /B php " . $script_location . " " . $query_string, "r"));
			} else {
				exec("php " . $script_location . " " . $query_string . " > /dev/null &");
			}
		}
	}
	
	/**
	 * Generate a secret to be used for validation
	 *
	 * @param int $entity_guid
	 * @return boolean|string
	 */
	function newsletter_generate_commanline_secret($entity_guid) {
		$result = false;
		
		$entity_guid = sanitise_int($entity_guid, false);
		
		if (!empty($entity_guid)) {
			$plugin = elgg_get_plugin_from_id("newsletter");
			
			$result = hash_hmac("sha256", ($plugin->getGUID() . "|" . $entity_guid . "|" . $plugin->time_created), get_site_secret());
		}
		
		return $result;
	}
	
	/**
	 * Validate if the provided secret is correct
	 *
	 * @param int $entity_guid
	 * @param string $secret
	 * @return boolean
	 */
	function newsletter_validate_commandline_secret($entity_guid, $secret) {
		$result = false;
		
		$entity_guid = sanitise_int($entity_guid, false);
		
		if (!empty($entity_guid) && !empty($secret)) {
			$correct_secret = newsletter_generate_commanline_secret($entity_guid);
			
			if (!empty($correct_secret) && ($correct_secret === $secret)) {
				$result = true;
			}
		}
		
		return $result;
	}
	
	function newsletter_process($entity_guid) {
		
		$entity_guid = sanitise_int($entity_guid, false);
		
		if (!empty($entity_guid)) {
			// ignore access
			$ia = elgg_set_ignore_access(true);
			
			$entity = get_entity($entity_guid);
			// is this a Newsletter
			if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
				// set newsletter status to sending
				
				// get the recipients
				
				// get newsletter content
				
				// create individual footer for unsubscribe link
				
				// send mail
				
				// set newsletter status to done
			}
			
			// restore access
			elgg_set_ignore_access($ia);
		}
	}
	