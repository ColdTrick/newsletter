<?php

	elgg_make_sticky_form("newsletter_schedule");

	$guid = (int) get_input("guid");
	$date = (int) get_input("date");
	$hour = (int) get_input("hour");
	
	$forward_url = REFERER;
	
	if (!empty($guid) && !empty($date)) {
		$entity = get_entity($guid);
	
		if (!empty($entity) && $entity->canEdit()) {
			if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
				
				// convert date from GMT to local
				// and add hours
				$date = mktime($hour, 0, 0, date("n", $date), date("j", $date), date("Y", $date));
				
				$entity->scheduled = $date;
				
				// some cleanup
				system_message(elgg_echo("newsletter:action:schedule:success"));
				
				elgg_clear_sticky_form("newsletter_schedule");
				if (elgg_instanceof($entity->getContainerEntity(), "group")) {
					$forward_url = "newsletter/group/" . $entity->getContainerGUID();
				} else {
					$forward_url = "newsletter/site";
				}
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