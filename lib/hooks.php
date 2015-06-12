<?php
/**
 * All plugin hook handlers will be defined here
 */

/**
 * The cron hook will take care of sending all the scheduled newsletters
 *
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param string $returnvalue returnvalue of the hook
 * @param array  $params      params of the hook
 *
 * @return void
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
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue returnvalue of the hook
 * @param array  $params      params of the hook
 *
 * @return array					write access array
 */
function newsletter_write_access_handler($hook, $type, $returnvalue, $params) {
	$result = $returnvalue;
	
	if (elgg_is_logged_in() && elgg_in_context("newsletter")) {
		$page_owner = elgg_get_page_owner_entity();
		
		if (!empty($page_owner)) {
			$allowed_keys = array(
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
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return array Menu items
 */
function newsletter_register_page_menu_handler($hook, $type, $returnvalue, $params) {
	$result = $returnvalue;
	
	$user = elgg_get_logged_in_user_entity();
	
	if (!empty($user) && elgg_in_context("newsletter")) {
		// link to your subscriptions
		$result[] = ElggMenuItem::factory(array(
			"name" => "newsletter_suscriptions",
			"href" => "newsletter/subscriptions/" . $user->username,
			"text" => elgg_echo("newsletter:menu:page:subscriptions"),
			"is_trusted" => true
		));
		$result[] = ElggMenuItem::factory(array(
			"name" => "newsletter_received",
			"href" => "newsletter/received/" . $user->username,
			"text" => elgg_echo("newsletter:menu:page:received"),
			"is_trusted" => true
		));
	}
	
	// settings pages
	$page_owner = elgg_get_page_owner_entity();
	if (elgg_in_context("settings") && !empty($user) && !empty($page_owner)) {
		if (elgg_instanceof($page_owner, "user") && $page_owner->canEdit()) {
			$result[] = ElggMenuItem::factory(array(
				"name" => "edit",
				"href" => "newsletter/subscriptions/" . $page_owner->username,
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
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return array Menu items
 */
function newsletter_register_newsletter_steps_menu_handler($hook, $type, $returnvalue, $params) {
	
	$entity = elgg_extract("entity", $params);
	
	if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
		// basic info
		$returnvalue[] = ElggMenuItem::factory(array(
			"name" => "basic",
			"href" => "newsletter/edit/" . $entity->getGUID(),
			"text" => elgg_view_icon("checkmark", "mrs") . elgg_echo("newsletter:menu:steps:entity")
		));
		
		// template
		$item = ElggMenuItem::factory(array(
			"name" => "template",
			"href" => "newsletter/edit/" . $entity->getGUID() . "/template",
			"text" => elgg_echo("newsletter:menu:steps:template")
		));
		
		if ($entity->template) {
			$item->setText(elgg_view_icon("checkmark", "mrs") . $item->getText());
		}
		
		$returnvalue[] = $item;
		
		// content
		$item = ElggMenuItem::factory(array(
			"name" => "content",
			"href" => "newsletter/edit/" . $entity->getGUID() . "/content",
			"text" => elgg_echo("newsletter:menu:steps:content")
		));
		
		if ($entity->content) {
			$item->setText(elgg_view_icon("checkmark", "mrs") . $item->getText());
		}
		
		$returnvalue[] = $item;
		
		// recipients
		$item = ElggMenuItem::factory(array(
			"name" => "recipients",
			"href" => "newsletter/edit/" . $entity->getGUID() . "/recipients",
			"text" => elgg_echo("newsletter:menu:steps:recipients")
		));
		
		if ($entity->getRecipients()) {
			$item->setText(elgg_view_icon("checkmark", "mrs") . $item->getText());
		}
			
		$returnvalue[] = $item;
			
		// schedule
		$item = ElggMenuItem::factory(array(
			"name" => "schedule",
			"href" => "newsletter/edit/" . $entity->getGUID() . "/schedule",
			"text" => elgg_echo("newsletter:menu:steps:schedule")
		));
		
		if ($entity->scheduled) {
			$item->setText(elgg_view_icon("checkmark", "mrs") . $item->getText());
		}
		
		$returnvalue[] = $item;
	} else {
		// basic info
		$returnvalue[] = ElggMenuItem::factory(array(
			"name" => "basic",
			"href" => "javascript:void(0);",
			"text" => elgg_echo("newsletter:menu:steps:entity"),
			"selected" => true
		));
		
		// template
		$returnvalue[] = ElggMenuItem::factory(array(
			"name" => "template",
			"href" => "javascript:void(0);",
			"text" => elgg_echo("newsletter:menu:steps:template")
		));
		
		// content
		$returnvalue[] = ElggMenuItem::factory(array(
			"name" => "content",
			"href" => "javascript:void(0);",
			"text" => elgg_echo("newsletter:menu:steps:content")
		));
		// recipients
		$returnvalue[] = ElggMenuItem::factory(array(
			"name" => "recipients",
			"href" => "javascript:void(0);",
			"text" => elgg_echo("newsletter:menu:steps:recipients")
		));
		
		// schedule
		$returnvalue[] = ElggMenuItem::factory(array(
			"name" => "schedule",
			"href" => "javascript:void(0);",
			"text" => elgg_echo("newsletter:menu:steps:schedule")
		));
	}
	
	return $returnvalue;
}

/**
 * Check if there is a email subscription for the user's email address
 * If so, convert the settings to the user and remove the email subscription
 *
 * @param string $hook        Which hook was triggered
 * @param string $type        What was the type of hook
 * @param array  $returnvalue null
 * @param array  $params      null
 *
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
 * @param string $hook        Which hook was triggered
 * @param string $type        What was the type of hook
 * @param bool   $returnvalue you can stop the registration proccess
 * @param array  $params      different variables, including the new user
 *
 * @return void
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

/**
 * Add a menu item in the entity's menu
 *
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return array Menu items
 */
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
				
				$result[] = ElggMenuItem::factory(array(
					"name" => "duplicate",
					"href" => "action/newsletter/duplicate?guid=" . $entity->getGUID(),
					"text" => elgg_echo("newsletter:menu:entity:duplicate"),
					"is_action" => true,
					"is_trusted" => true
				));
			}
		}
	}
	
	return $result;
}

/**
 * Handles newsletter icon urls
 *
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return string url
 */
function newsletter_icon_hook($hook, $type, $returnvalue, $params) {
	if (!empty($params) && is_array($params)) {
		$entity = $params["entity"];
			
		if (elgg_instanceof($entity, "object", "newsletter")) {
			$url = elgg_get_site_url() . "mod/newsletter/_graphics/newsletter-icon.png";
			
			return $url;
		}
	}
}

/**
 * Add a menu item in the owner block menu of a group
 *
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return array Menu items
 */
function newsletter_register_owner_block_menu_handler($hook, $type, $returnvalue, $params) {
	$result = $returnvalue;
	
	if (!empty($params) && is_array($params)) {
		$entity = elgg_extract("entity", $params);
		
		if (!empty($entity) && elgg_instanceof($entity, "group")) {
			if (newsletter_is_group_enabled($entity)) {
				$result[] = ElggMenuItem::factory(array(
					"name" => "newsletter",
					"text" => elgg_echo("newsletter:menu:owner_block:group"),
					"href" => "newsletter/group/" . $entity->getGUID(),
					"is_trusted" => true
				));
			}
		}
	}
	
	return $result;
}

/**
 * Replace the filter menu on the newsletter pages
 *
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return array Menu items
 */
function newsletter_register_filter_menu_handler($hook, $type, $returnvalue, $params) {
	$result = $returnvalue;
	
	if (elgg_in_context("newsletter")) {
		$result = array();
		
		$page_owner = elgg_get_page_owner_entity();
		
		if (elgg_is_admin_logged_in() || (!empty($page_owner) && $page_owner->canEdit())) {
			$base_url = "newsletter/site";
			if (elgg_instanceof($page_owner, "group")) {
				$base_url = "newsletter/group/" . $page_owner->getGUID();
			}
			$current_filter = get_input("filter", "sent");
			
			$result[] = ElggMenuItem::factory(array(
				"name" => "concept",
				"text" => elgg_echo("newsletter:menu:filter:concept"),
				"href" => $base_url . "?filter=concept",
				"is_trusted" => true,
				"selected" => ($current_filter == "concept")
			));
			
			$result[] = ElggMenuItem::factory(array(
				"name" => "scheduled",
				"text" => elgg_echo("newsletter:menu:filter:scheduled"),
				"href" => $base_url . "?filter=scheduled",
				"is_trusted" => true,
				"selected" => ($current_filter == "scheduled")
			));
			
			$result[] = ElggMenuItem::factory(array(
				"name" => "sending",
				"text" => elgg_echo("newsletter:menu:filter:sending"),
				"href" => $base_url . "?filter=sending",
				"is_trusted" => true,
				"selected" => ($current_filter == "sending")
			));
			
			$result[] = ElggMenuItem::factory(array(
				"name" => "sent",
				"text" => elgg_echo("newsletter:menu:filter:sent"),
				"href" => $base_url . "?filter=sent",
				"is_trusted" => true,
				"selected" => ($current_filter == "sent")
			));
		}
	}
	
	return $result;
}

/**
 * Add a menu item in the long text inputs (like embed and tinymce)
 *
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return array Menu items
 */
function newsletter_register_longtext_menu_handler($hook, $type, $returnvalue, $params) {

	$result = $returnvalue;
	$id = elgg_extract("id", $params);

	if (strpos($id, "newsletter-edit-content-") === 0) {
		if (newsletter_embed_available()) {
			$guid = str_replace("newsletter-edit-content-", "", $id);

			$result[] = ElggMenuItem::factory(array(
				"name" => "newsletter-embed-content",
				"href" => "newsletter/embed/" . $guid,
				"text" => elgg_echo("newsletter:menu:longtext:embed_content"),
				"link_class" => "elgg-longtext-control elgg-lightbox",
				"priority" => 5,
			));
				
			elgg_load_js("lightbox");
			elgg_load_css("lightbox");
			elgg_require_js("newsletter/embed");
		}
	}
	
	return $result;
}

/**
 * Add a menu item in the buttons menu of the online/preview view
 *
 * @param string $hook        name of the hook
 * @param string $type        type of the hook
 * @param array  $returnvalue Default menu items
 * @param array  $params      params for the hook
 *
 * @return array Menu items
 */
function newsletter_register_buttons_menu_handler($hook, $type, $returnvalue, $params) {
	$result = $returnvalue;
	
	if (!empty($params) && is_array($params)) {
		$entity = elgg_extract("entity", $params);
		
		if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			$container = $entity->getContainerEntity();
			
			if ($container instanceof ElggGroup) {
				$href = "newsletter/group/" . $container->getGUID();
			} else {
				$href = "newsletter/site";
			}
			
			$referer = elgg_extract('HTTP_REFERER', $_SERVER);
			if (!empty($referer) && stristr($referer, elgg_get_site_url())) {
				// there is history to this site, so add a back button
				$result[] = ElggMenuItem::factory(array(
					"name" => "back",
					"href" => $referer,
					"text" => elgg_echo("back"),
					"target" => "_self"
				));
			}
			
			$result[] = ElggMenuItem::factory(array(
				"name" => "more",
				"href" => $href,
				"text" => elgg_echo("newsletter:menu:site"),
				"target" => "_self"
			));
			
			if ($entity->canEdit()) {
				$result[] = ElggMenuItem::factory(array(
					"name" => "edit",
					"href" => "newsletter/edit/" . $entity->getGUID(),
					"text" => elgg_echo("edit"),
					"target" => "_self"
				));
				
				if (elgg_extract("type", $params) == "preview") {
					$result[] = ElggMenuItem::factory(array(
						"name" => "mail",
						"href" => false,
						"text" => elgg_view_form("newsletter/preview_mail", array("target" => "_self"), array("entity" => $entity))
					));
				}
			}
		}
	}
	
	return $result;
}

/**
 * Extend public pages
 *
 * @param string   $hook_name    "public_pages"
 * @param string   $entity_type  "walled_garden"
 * @param string[] $return_value array of public pages
 * @param mixed    $params       unused
 *
 * @return string[]
 */
function newsletter_public_pages($hook_name, $entity_type, $return_value, $params) {
	$return = $return_value;
	if (is_array($return)) {
		$return[] = "newsletter/view/.*";
		$return[] = "newsletter/unsubscribe/.*";
		$return[] = "action/newsletter/unsubscribe";
	}
	return $return;
}

/**
 * Provide an URL for a widget title
 *
 * @param string $hook_name    "widget_url"
 * @param string $entity_type  "widget_manager"
 * @param string $return_value the current url (if any)
 * @param mixed  $params       provided params
 *
 * @return string
 */
function newsletter_widget_url_handler($hook_name, $entity_type, $return_value, $params) {
	$result = $return_value;
	
	if (empty($result) && !empty($params) && is_array($params)) {
		$user = elgg_get_logged_in_user_entity();
		$widget = elgg_extract("entity", $params);
		
		if (!empty($user) && !empty($widget) && elgg_instanceof($widget, "object", "widget")) {
			switch ($widget->handler) {
				case "newsletter_subscribe":
					$result = "newsletter/subscriptions/" . $user->username;
					break;
			}
		}
	}
	
	return $result;
}

/**
 * Modify the tag_tools type/subtypes for notifications
 *
 * @param string $hook_name    the name of the hook
 * @param string $entity_type  the type of the hook
 * @param array  $return_value current return value
 * @param array  $params       supplied params
 *
 * @return array|void
 */
function newsletter_tag_tools_notifications($hook_name, $entity_type, $return_value, $params) {
	
	if (empty($return_value) || !is_array($return_value)) {
		return;
	}
	
	$object_subtypes = elgg_extract('object', $return_value);
	if (empty($object_subtypes) || !is_array($object_subtypes)) {
		return;
	}
	
	$key = array_search(Newsletter::SUBTYPE, $object_subtypes);
	if ($key === false) {
		return;
	}
	
	unset($object_subtypes[$key]);
	$return_value['object'] = array_values($object_subtypes);
	
	return $return_value;
}
