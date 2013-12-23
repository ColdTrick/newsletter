<?php
/**
 * Duplicate a newsletter for later use
 *
 */

$guid = (int) get_input("guid");

$forward_url = REFERER;

if (!empty($guid)) {
	$entity = get_entity($guid);

	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			$clone = clone $entity;
			
			if ($clone->save()) {
				// forward to the edit page so you can start working with the clone
				$forward_url = "newsletter/edit/" . $clone->getGUID();
				
				system_message(elgg_echo("newsletter:action:duplicate:success"));
			} else {
				register_error(elgg_echo("newsletter:action:duplicate:error"));
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