<?php

	/**
	 * The cron hook will take care of sending all the scheduled newsletters
	 *
	 * @param string $hook
	 * @param string $type
	 * @param string $returnvalue
	 * @param array $params
	 */
	function newsletter_cron_handler($hook, $type, $returnvalue, $params) {
		
		if (!empty($params) && is_array($params)) {
			$cron_ts = elgg_extract("time", $params, time());
			
			// check for time drift
			if (date("i", $cron_ts) >= 30) {
				// example of the time: 14:59:56
				// which should be the hourly cron for 15:00:00
				$cron_ts = $cron_ts + (30 * 60);
			}
			
			// make the timestamp to an hour
			$newsletter_ts = mktime(date("H", $cron_ts), 0, 0, date("n", $cron_ts), date("j", $cron_ts), date("Y", $cron_ts));
			
			$options = array(
				"type" => "object",
				"subtype'" => Newsletter::SUBTYPE,
				"limit" => false,
				"metadata_name_value_pairs" => array(
					"name" => "scheduled",
					"value" => $newsletter_ts
				)
			);
			
			// ignore access
			$ia = elgg_set_ignore_access(true);
			
			$newsletters = elgg_get_entities_from_metadata($options);
			
			// retore access
			elgg_set_ignore_access($ia);
			
			if (!empty($newsletters)) {
				foreach ($newsletters as $newsletter) {
					newsletter_start_commandline_sending($newsletter);
				}
			}
		}
	}
	
	/**
	 * Remove some options from the write access array if it's a newsletter
	 *
	 * @param string $hook
	 * @param string $type
	 * @param string $returnvalue
	 * @param array $params
	 * @return array
	 */
	function newsletter_write_access_handler($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		if (elgg_is_logged_in() && elgg_in_context("newsletter")) {
			$page_owner = elgg_get_page_owner_entity();
			
			if (!empty($page_owner)) {
				$allowed_keys = array(
					ACCESS_PRIVATE,
					ACCESS_LOGGED_IN,
					ACCESS_PUBLIC
				);
				
				if (elgg_instanceof($page_owner, "group")) {
					$allowed_keys[] = $page_owner->group_acl;
				}
				
				foreach ($result as $access_id => $label) {
					if (!in_array($access_id, $allowed_keys)) {
						unset($result[$access_id]);
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Add a menu item in the sidebar to go to the newsletter subsciptions
	 *
	 * @param 	string	$hook
	 * @param 	string 	$type
	 * @param 	array 	$returnvalue	Default menu items
	 * @param 	array 	$params
	 * @return 	array					Menu items
	 */
	function newsletter_register_page_menu_handler($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		$user = elgg_get_logged_in_user_entity();
		
		if (!empty($user) && elgg_in_context("newsletter")) {
			// link to your subscriptions
			$result[] = ElggMenuItem::factory(array(
				"name" => "newsletter_suscriptions",
				"href" => "newsletter/subscriptions/" . $user->getGUID(),
				"text" => elgg_echo("newsletter:menu:page:subscriptions"),
				"is_trusted" => true
			));
		}
		
		// settings pages
		$page_owner = elgg_get_page_owner_entity();
		if (elgg_in_context("settings") && !empty($user) && !empty($page_owner)) {
			if (elgg_instanceof($page_owner, "user") && $page_owner->canEdit()) {
				$result[] = ElggMenuItem::factory(array(
					"name" => "edit",
					"href" => "newsletter/subscriptions/" . $page_owner->getGUID(),
					"text" => elgg_echo("newsletter:menu:page:settings"),
					"is_trusted" => true
				));
			}
		}
		
		return $result;
	}
	
	/**
	 * Add a menu item in the sidebar for the steps of creating a newsletter
	 *
	 * @param 	string	$hook
	 * @param 	string 	$type
	 * @param 	array 	$returnvalue	Default menu items
	 * @param 	array 	$params
	 * @return 	array					Menu items
	 */
	function newsletter_register_newsletter_steps_menu_handler($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		$entity = $params["entity"];
		
		if ($entity) {
			$result[] = ElggMenuItem::factory(array(
				"name" => "basic",
				"href" => "newsletter/edit/" . $entity->getGUID(),
				"text" => elgg_echo("newsletter:menu:steps:entity") . elgg_view_icon("checkmark", "float-alt")
			));
			
			$item = ElggMenuItem::factory(array(
				"name" => "template",
				"href" => "newsletter/edit/" . $entity->getGUID() . "/template",
				"text" => elgg_echo("newsletter:menu:steps:template")
			));
			
			if ($entity->template) {
				$item->setText($item->getText() . elgg_view_icon("checkmark", "float-alt"));
			}
			
			$result[] = $item;
			
			$item = ElggMenuItem::factory(array(
				"name" => "content",
				"href" => "newsletter/edit/" . $entity->getGUID() . "/content",
				"text" => elgg_echo("newsletter:menu:steps:content")
			));
			
			if ($entity->content) {
				$item->setText($item->getText() . elgg_view_icon("checkmark", "float-alt"));
			}
			
			$result[] = $item;
			
			$item = ElggMenuItem::factory(array(
				"name" => "recipients",
				"href" => "newsletter/edit/" . $entity->getGUID() . "/recipients",
				"text" => elgg_echo("newsletter:menu:steps:recipients")
			));
			
			if ($entity->recipients) {
				$item->setText($item->getText() . elgg_view_icon("checkmark", "float-alt"));
			}
				
			$result[] = $item;
				
			$item = ElggMenuItem::factory(array(
				"name" => "schedule",
				"href" => "newsletter/edit/" . $entity->getGUID() . "/schedule",
				"text" => elgg_echo("newsletter:menu:steps:schedule")
			));
			
			if ($entity->scheduled) {
				$item->setText($item->getText() . elgg_view_icon("checkmark", "float-alt"));
			}
			
			$result[] = $item;
		}
		
		return $result;
	}
	
	/*
	 * Check if there is a email subscription for the user's email address
	 * If so, convert the settings to the user and remove the email subscription
	 *
	 * @param 	string	$hook			Which hook was triggered
	 * @param 	string 	$type			What was the type of hook
	 * @param 	array 	$returnvalue	null
	 * @param 	array 	$params			null
	 * @return 	void
	 */
	function newsletter_usersettings_save_handler($hook, $type, $returnvalue, $params) {
		$user_guid = (int) get_input("guid");
		
		$user = get_user($user_guid);
		if (!empty($user)) {
			$subscription = newsletter_get_subscription($user->email);
			
			if (!empty($subscription)) {
				newsletter_convert_subscription_to_user_setting($subscription, $user);
			}
		}
	}
	
	/**
	 * A hook fired during the registration proccess of the user
	 * Check if the user wants to receive site newsletters
	 *
	 * @param 	string	$hook			Which hook was triggered
	 * @param 	string 	$type			What was the type of hook
	 * @param 	bool 	$returnvalue	you can stop the registration proccess
	 * @param 	array 	$params			different variables, including the new user
	 */
	function newsletter_register_user_handler($hook, $type, $returnvalue, $params) {
		
		if (!empty($params) && is_array($params)) {
			$user = elgg_extract("user", $params);
			
			if (!empty($user) && elgg_instanceof($user, "user")) {
				$site = elgg_get_site_entity();
				$subscribe = (int) get_input("newsletter_subscription");
				
				if (!empty($subscribe)) {
					// user wants to receive newsletter
					newsletter_subscribe_user($user, $site);
				} else {
					// user doesn't want to recieve newsletter
					newsletter_unsubscribe_user($user, $site);
				}
			}
		}
	}
	
	function newsletter_register_entity_menu_handler($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		if (!empty($params) && is_array($params)) {
			$entity = elgg_extract("entity", $params);
			
			if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
				if ($entity->canEdit()) {
					if (($entity->status == "sent") || $entity->getLogging()) {
						$item = ElggMenuItem::factory(array(
							"name" => "log",
							"href" => "newsletter/log/" . $entity->getGUID(),
							"text" => elgg_echo("newsletter:menu:entity:log")
						));
							
						$result[] = $item;
					}
				}
			}
		}
		
		return $result;
	}
