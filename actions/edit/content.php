<?php
$guid = (int) get_input("guid");
$content = get_input("content", "", false);

$forward_url = REFERER;

if (!empty($guid) && !empty($content)) {
	$entity = get_entity($guid);

	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			
			if (empty($entity->content)) {
				$forward_url = "newsletter/edit/" . $entity->getGUID() . "/recipients";
			}
			
			$entity->content = $content;

			// some cleanup
			system_message(elgg_echo("newsletter:action:content:success"));

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