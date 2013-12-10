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
	
	/**
	 * Format a recipient to be listed in the recipient autocomplete or listing
	 *
	 * @param 	mixed	$recipient	Can be an ElggUser, ElggGroup or email address
	 * @return 	array	Contains, of false on failure
	 * 						- type : user, group or email
	 * 						- label: used in the autocomplete dropdown
	 * 						- content: used in the listing view
	 * 						- value: eighter a guid of email address
	 */
	function newsletter_format_recipient($recipient) {
		$result = false;
		
		$regexpr = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
		
		if (!empty($recipient)) {
			if (elgg_instanceof($recipient, "user")) {
				$icon = elgg_view_entity_icon($recipient, "tiny", array(
					"use_hover" => false,
					"use_link" => false
				));
				
				// label for autoselect listing
				$label = elgg_view_image_block($icon, $recipient->name, array("class" => "elgg-autocomplete-item"));
				
				// content for if selected
				$content = "<div class='newsletter-recipient-result phs'>";
				$content .= elgg_view("input/hidden", array("name" => "user_guids[]", "value" => $recipient->getGUID()));
				$content .= elgg_view_image_block($icon, $recipient->name, array("image_alt" => elgg_view_icon("delete-alt")));
				$content .= "</div>";
				
				$result = array(
					"type" => "user",
					"value" => $recipient->getGUID(),
					"label" => $label,
					"content" => $content
				);
			} elseif (elgg_instanceof($recipient, "group")) {
				$icon = elgg_view_entity_icon($recipient, "tiny", array(
					"use_hover" => false,
					"href" => false
				));
				
				// label for autoselect listing
				$name = "<span>" . elgg_echo("groups:group") . ": </span>" . $recipient->name;
				$name .= "<span class='elgg-quiet'> (" . $recipient->getMembers(0, 0, true) . " " . elgg_echo("groups:member") . ")</span>";
				
				$label = elgg_view_image_block($icon, $name, array("class" => "elgg-autocomplete-item newsletter-recipient-autocomplete-group"));
				
				// content for if selected
				$name = $recipient->name;
				$name .= "<span class='elgg-quiet'> (" . $recipient->getMembers(0, 0, true) . " " . elgg_echo("groups:member") . ")</span>";
				
				$content = "<div class='newsletter-recipient-result phs'>";
				$content .= elgg_view("input/hidden", array("name" => "group_guids[]", "value" => $recipient->getGUID()));
				$content .= elgg_view_image_block($icon, $name, array("image_alt" => elgg_view_icon("delete-alt")));
				$content .= "</div>";
				
				$result = array(
					"type" => "group",
					"value" => $recipient->getGUID(),
					"label" => $label,
					"content" => $content
				);
			} elseif (preg_match($regexpr, $recipient)) {
				$name = "<span>" . elgg_echo("newsletter:recipients:email") . ": </span>" . $recipient;
				$label = elgg_view_image_block("", $name, array("class" => "elgg-autocomplete-item newsletter-recipient-autocomplete-email"));
					
				$content = "<div class='newsletter-recipient-result phs'>";
				$content .= elgg_view("input/hidden", array("name" => "emails[]", "value" => $recipient));
				$content .= elgg_view_image_block("", $recipient, array("image_alt" => elgg_view_icon("delete-alt")));
				$content .= "</div>";
					
				$result = array(
					"type" => "email",
					"label" => $label,
					"content" => $content
				);
			}
		}
		
		return $result;
	}
	
	/**
	 * Get all the people that subscribed to the newsletter of this container
	 *
	 * @param 	ElggEntity 	$container	Which container
	 * @param 	bool		$count		Return just a count, not the actual subscribers
	 * @return 	array | int	On success, of false on failure
	 */
	function newsletter_get_subscribers(ElggEntity $container, $count = false) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group"))) {
			// get the subscribers
			if (!$count) {
				
			} else {
				$result = 0;
			}
		}
		
		return $result;
	}
	