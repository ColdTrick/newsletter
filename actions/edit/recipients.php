<?php

	elgg_make_sticky_form("newsletter_recipients");
	
	$guid = (int) get_input("guid");
	
	$user_guids = get_input("user_guids");
	$group_guids = get_input("group_guids");
	$emails = get_input("emails");
	
	$subscribers = (int) get_input("subscribers");
	$members = (int) get_input("members");
	
	$forward_url = REFERER;
	
	if (!empty($guid)) {
		$entity = get_entity($guid);
	
		if (!empty($entity) && $entity->canEdit()) {
			if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
				
				$tmp = array(
					"user_guids" => $user_guids,
					"group_guids" => $group_guids,
					"emails" => $emails,
					"subscribers" => $subscribers,
					"members" => $members
				);
				
				$entity->recipients = json_encode($tmp);
				
				system_message(elgg_echo("newsletter:action:recipients:success"));
				
				elgg_clear_sticky_form("newsletter_recipients");

			} else {
				register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
			}
		} else {
			register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward($forward_url);