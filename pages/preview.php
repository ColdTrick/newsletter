<?php

gatekeeper();

$guid = (int) get_input("guid");

// validate input
if (empty($guid)) {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	forward(REFERER);
}

if (!elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
	register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
	forward(REFERER);
}

echo elgg_view_layout("newsletter", array("entity" => $entity));
