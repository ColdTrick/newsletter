<?php

/**
 * Action file to directly send a newsletter to its recipients
 *
 * @uses get_input('guid')	the GUID of the newsletter to be send
 */

$guid = (int) get_input("guid");

$forward_url = REFERER;

if (!empty($guid)) {
	$entity = get_entity($guid);
	
	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			$entity->scheduled = mktime(date("G"), 0, 0, date("n"), date("j"), date("Y"));
			
			newsletter_start_commandline_sending($entity);
			
			if (elgg_instanceof($entity->getContainerEntity(), "site")) {
				$forward_url = "newsletter/site";
			} else {
				$forward_url = "newsletter/group/" . $entity->getContainerGUID();
			}
			
			system_message(elgg_echo("newsletter:action:send:success"));
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
