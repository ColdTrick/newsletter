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
			} elseif (newsletter_is_email_address($recipient)) {
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
	 * @return 	array | int				On success or false on failure
	 */
	function newsletter_get_subscribers(ElggEntity $container, $count = false) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group"))) {
			// get the subscribers
			if (!$count) {
				$result = array(
					"users" => array(),
					"emails" => array()
				);
				
				// get all subscribed community members
				$options = array(
					"type" => "user",
					"selects" => array("ue.email"),
					"site_guids" => false,
					"limit" => false,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true,
					"joins" => array("JOIN " . elgg_get_config("dbprefix") . "users_entity ue ON e.guid = ue.guid"),
					"callback" => "newsletter_user_row_to_subscriber_info"
				);
				
				// @todo make this easier????
				$tmp_users = elgg_get_entities_from_relationship($options);
				if (!empty($tmp_users)) {
					foreach ($tmp_users as $tmp_user) {
						$result["users"][$tmp_user["guid"]] = $tmp_user["email"];
					}
				}
				
				// check the email subscriptions
				$options = array(
					"type" => "object",
					"subtype" => NewsletterSubscription::SUBTYPE,
					"selects" => array("oe.title"),
					"limit" => false,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true,
					"joins" => array("JOIN " . elgg_get_config("dbprefix") . "objects_entity oe ON e.guid = oe.guid"),
					"callback" => "newsletter_subscription_row_to_subscriber_info"
				);
				
				$result["emails"] = elgg_get_entities_from_relationship($options);
			} else {
				// get all subscribed community members
				$options = array(
					"type" => "user",
					"site_guids" => false,
					"count" => true,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true
				);
				
				$result = elgg_get_entities_from_relationship($options);
				
				// check the email subscriptions
				$options = array(
					"type" => "object",
					"subtype" => NewsletterSubscription::SUBTYPE,
					"count" => true,
					"relationship" => NewsletterSubscription::SUBSCRIPTION,
					"relationship_guid" => $container->getGUID(),
					"inverse_relationship" => true,
				);
				
				$result += elgg_get_entities_from_relationship($options);
			}
		}
		
		return $result;
	}
	
	/**
	 * Check if a user is subscribed to a container entity
	 *
	 * @param 	ElggUser 	$user	The user to check
	 * @param 	ElggEntity 	$entity	The container entity to check against
	 * @return 	boolean				True => the user has a subscription, false => no subscription or error
	 */
	function newsletter_check_user_subscription(ElggUser $user, ElggEntity $entity) {
		$result = false;
		
		if (!empty($user) && !empty($entity)) {
			if (elgg_instanceof($user, "user") && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				$result = (bool) check_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID());
			}
		}
		
		return $result;
	}
	
	/**
	 * Add a subscription for an user to a container
	 *
	 * @param 	ElggUser 	$user	The user to subscribe
	 * @param 	ElggEntity 	$entity	The container entity to subscribe to
	 * @return 	boolean				true on success else false
	 */
	function newsletter_subscribe_user(ElggUser $user, ElggEntity $entity) {
		$result = false;
		
		if (!empty($user) && !empty($entity)) {
			if (elgg_instanceof($user, "user") && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// check if subscribed
				if (!check_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID())) {
					// not yet, so add
					$result = add_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID());
				} else {
					$result = true;
				}
				
				// remove blocklist relation
				remove_entity_relationship($user->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID());
				
				// remove general blocklist
				$site = elgg_get_site_entity();
				remove_entity_relationship($user->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
				
				// check if on email blacklist
				$subscription = newsletter_get_subscription($user->email);
				
				if (!empty($subscription)) {
					$subscription->removeRelationship($entity, NewsletterSubscription::BLACKLIST);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Add a subscription for an email address to a container
	 *
	 * @param 	string 		$email	The email address to add to the subscriptions
	 * @param 	ElggEntity 	$entity	The container entity to subscribe to
	 * @return 	boolean				true on success else false
	 */
	function newsletter_subscribe_email($email, ElggEntity $entity) {
		$result = false;
		
		if (!empty($email) && !empty($entity)) {
			if (newsletter_is_email_address($email) && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// check if email belongs to existing user
				$users = get_user_by_email($email);
				
				if (!empty($users)) {
					$result = newsletter_subscribe_user($users[0], $entity);
				} else {
					// check if email address exists in the system
					$subscription = newsletter_get_subscription($email);
					
					if (empty($subscription)) {
						$subscription = new NewsletterSubscription();
						$subscription->title = $email;
						
						if (!$subscription->save()) {
							return false;
						}
					}
					
					// subscribe
					$result = (bool) $subscription->addRelationship($entity->getGUID(), NewsletterSubscription::SUBSCRIPTION);
					
					// remove blocklist relation
					remove_entity_relationship($subscription->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID());
					
					// remove general blocklist
					$site = elgg_get_site_entity();
					remove_entity_relationship($user->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Remove a subscription for an user to a container
	 *
	 * @param 	ElggUser 	$user	The user to unsubscribe
	 * @param 	ElggEntity 	$entity	The container entity to unsubscribe from
	 * @return 	boolean				true on success else false
	 */
	function newsletter_unsubscribe_user(ElggUser $user, ElggEntity $entity) {
		$result = false;
		
		if (!empty($user) && !empty($entity)) {
			if (elgg_instanceof($user, "user") && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// remove subscription
				remove_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity->getGUID());
				
				// check if on email subscriptionlist
				$subscription = newsletter_get_subscription($user->email);
				
				if (!empty($subscription)) {
					$subscription->removeRelationship($entity->getGUID(), NewsletterSubscription::SUBSCRIPTION);
				}
		
				// check if blocked
				if (!check_entity_relationship($user->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID())) {
					// not yet, so add
					$result = add_entity_relationship($user->getGUID(), NewsletterSubscription::BLACKLIST, $entity->getGUID());
				} else {
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Remove a subscription for an email address to a container
	 *
	 * @param 	string		$email	The email address to remove from the subscriptions
	 * @param 	ElggEntity 	$entity	The container entity to unsubscribe from
	 * @return 	boolean				true on success else false
	 */
	function newsletter_unsubscribe_email($email, ElggEntity $entity) {
		$result = false;
		
		if (!empty($email) && !empty($entity)) {
			if (newsletter_is_email_address($email) && (elgg_instanceof($entity, "site") || elgg_instanceof($entity, "group"))) {
				// check if not existing user
				$users = get_user_by_email($email);
				
				if (!empty($users)) {
					// existing user
					$result = newsletter_unsubscribe_user($users[0], $entity);
				} else {
					// email address
					$subscription = newsletter_get_subscription($email);
					
					if (empty($subscription)) {
						$subscription = new NewsletterSubscription();
						$subscription->title = $email;
						
						if (!$subscription->save()) {
							return false;
						}
					}
					
					// remove existing subscription (if any)
					$subscription->removeRelationship($entity->getGUID(), NewsletterSubscription::SUBSCRIPTION);
					
					// add to blocked list
					$result = $subscription->addRelationship($entity->getGUID(), NewsletterSubscription::BLACKLIST);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Custom callback for elgg_get_* function to return a subset of information about an user
	 *
	 * @param stdObj	$row	A database row
	 * @return	array			contains [guid] => email
	 *
	 * @see elgg_get_entities()
	 */
	function newsletter_user_row_to_subscriber_info($row) {
		return array(
			"guid" => (int) $row->guid,
			"email" => $row->email
		);
	}
	
	/**
	 * Custom callback for elgg_get_* function to return the email address of a subscriber
	 *
	 * @param 	stdObj	$row	A database row
	 * @return	string			the email address of the subscriber
	 *
	 * @see elgg_get_entities()
	 */
	function newsletter_subscription_row_to_subscriber_info($row) {
		return $row->title;
	}
	
	/**
	 * Custom callback for elgg_get_* function to return the GUID of an entity
	 *
	 * @param 	stdObj	$row	A database row
	 * @return	int				The GUID of the entity
	 *
	 * @see elgg_get_entities()
	 */
	function newsletter_row_to_guid($row) {
		return (int) $row->guid;
	}
	
	/**
	 * A different interpretation of is_email_address()
	 * because PHP doesn't always correctly verify email addresses
	 *
	 * @param 	string 	$address	The email address to check
	 * @return 	bool				true if email, false otherwise
	 *
	 * @see is_email_address()
	 * @see filter_var()
	 */
	function newsletter_is_email_address($address) {
		$result = false;
		
		if (!empty($address)) {
			$regexpr = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
			
			$result = (bool) preg_match($regexpr, $address);
		}
		
		return $result;
	}
	
	/**
	 * Generate an URL so the recipient can directly unsubscribe from a newsletter
	 *
	 * @param 	ElggEntity 	$container	Which newsletter container (ElggSite or ElggGroup)
	 * @param 	string|int 	$recipient	The user_guid or email address of the recipient
	 * @return 	bool|string				The unsubscribe link or false on failure
	 */
	function newsletter_generate_unsubscribe_link(ElggEntity $container, $recipient) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group")) && !empty($recipient)) {
			$code = newsletter_generate_unsubscribe_code($container, $recipient);
			
			if (is_numeric($recipient)) {
				// recipient is an user_guid
				$result = "newsletter/unsubscribe/" . $container->getGUID() . "?u=" . $recipient . "&c=" . $code;
			} elseif (newsletter_is_email_address($recipient)) {
				// recipient is an email address
				$result = "newsletter/unsubscribe/" . $container->getGUID() . "?e=" . $recipient . "&c=" . $code;
			}
		}
		
		return $result;
	}
	
	/**
	 * Generate a unsubscribe code to be used in validation
	 *
	 * @param 	ElggEntity 	$container	Which newsletter container (ElggSite or ElggGroup)
	 * @param 	string|int	$recipient	The user_guid or email address of the recipient
	 * @return 	bool|string				The unsubscribe code or false on failure
	 */
	function newsletter_generate_unsubscribe_code(ElggEntity $container, $recipient) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group")) && !empty($recipient)) {
			// make sure we have a user_guid or email address
			if (is_numeric($recipient) || newsletter_is_email_address($recipient)) {
				$plugin = elgg_get_plugin_from_id("newsletter");
				
				$result = hash_hmac("sha256", ($container->getGUID() . "|" . $recipient . "|" . $plugin->time_created), get_site_secret());
			}
		}
		
		return $result;
	}
	
	/**
	 * Validate a provided unsubscribe code
	 *
	 * @param 	ElggEntity 	$container	Which newsletter container (ElggSite or ElggGroup)
	 * @param 	string|int	$recipient	The user_guid or email address of the recipient
	 * @param	string		$code		The unsubscribe code the recipient provided
	 * @return 	bool					true is valid or false on failure
	 */
	function newsletter_validate_unsubscribe_code(ElggEntity $container, $recipient, $code) {
		$result = false;
		
		if (!empty($container) && (elgg_instanceof($container, "site") || elgg_instanceof($container, "group")) && !empty($recipient)) {
			// make sure we have a user_guid or email address
			if (is_numeric($recipient) || newsletter_is_email_address($recipient)) {
				// generate the code as it should be
				$correct_code = newsletter_generate_unsubscribe_code($container, $recipient);
				
				// check for a match
				if ($code === $correct_code) {
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Find a subscription entity for the given email address
	 *
	 * @param 	string	$email	The email address to find the subscription for
	 * @return 	bool|NewsletterSubscription	The found subscription or false
	 */
	function newsletter_get_subscription($email) {
		$result = false;
		
		if (!empty($email) && newsletter_is_email_address($email)) {
			// ignore access
			$ia = elgg_set_ignore_access(true);
			
			$options = array(
				"type" => "object",
				"subtype" => NewsletterSubscription::SUBTYPE,
				"limit" => 1,
				"joins" => array("JOIN " . elgg_get_config("dbprefix") . "objects_entity oe ON e.guid = oe.guid"),
				"wheres" => array("(oe.title = '" . sanitise_string($email) . "')")
			);
			
			$entities = elgg_get_entities($options);
			
			if (!empty($entities)) {
				$result = $entities[0];
			}
			
			// restore access
			elgg_set_ignore_access($ia);
		}
		
		return $result;
	}
	
	/**
	 * Block an user from all newsletters
	 *
	 * @param 	ElggUser 	$user	The user to block
	 * @return 	boolean				True on success, false on failure
	 */
	function newsletter_unsubscribe_all_user(ElggUser $user) {
		$result = false;
		
		if (!empty($user) && elgg_instanceof($user, "user")) {
			$site = elgg_get_site_entity();
			// remove site subscription
			remove_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $site->getGUID());
			
			// remove all subscriptions
			$options = array(
				"type" => "group",
				"limit" => false,
				"relationship" => NewsletterSubscription::SUBSCRIPTION,
				"relationship_guid" => $user->getGUID(),
				"callback" => "newsletter_row_to_guid"
			);
			
			$entities = elgg_get_entities_from_relationship($options);
			
			if (!empty($entities)) {
				foreach ($entities as $entity_guid) {
					remove_entity_relationship($user->getGUID(), NewsletterSubscription::SUBSCRIPTION, $entity_guid);
				}
			}
			
			// add to general blacklist
			$result = (bool) add_entity_relationship($user->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
			
			// remove email subscriptions (if any)
			$subscription = newsletter_get_subscription($user->email);
			
			if (!empty($subscription)) {
				$subscription->delete();
			}
		}
		
		return $result;
	}
	
	/**
	 * Block an email address from all newsletters
	 *
	 * @param 	string 	$email	The email address to block
	 * @return 	boolean			True on success, false on failure
	 */
	function newsletter_unsubscribe_all_email($email) {
		$result = false;
		
		if (!empty($email) && newsletter_is_email_address($email)) {
			// get subscription
			$subscription = newsletter_get_subscription($email);
			
			if (empty($subscription)) {
				$subscription = new NewsletterSubscription();
				$subscription->title = $email;
				
				if (!$subscription->save()) {
					return false;
				}
				
				// remove all existing subscriptions
				remove_entity_relationships($subscription->getGUID(), NewsletterSubscription::SUBSCRIPTION);
				
				// add to general blacklist
				$site = elgg_get_site_entity();
					
				$result = (bool) add_entity_relationship($subscription->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
			}
		}
		
		return $result;
	}
	