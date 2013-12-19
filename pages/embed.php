<?php

$guid = (int) get_input("guid");

// validate input
if (empty($guid)) {
	echo elgg_echo("InvalidParameterException:MissingParameter");
	exit();
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	echo elgg_echo("InvalidParameterException:NoEntityFound");
	exit();
}

if (!elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
	echo elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE)));
	exit();
}

if (elgg_is_active_plugin("blog")) {
	echo elgg_view("newsletter/embed", array("entity" => $entity));
}
