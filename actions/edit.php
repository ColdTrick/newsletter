<?php

elgg_make_sticky_form("newsletter_edit");

$guid = (int) get_input("guid");
$container_guid = (int) get_input("container_guid");
$title = get_input("title");
$subject = get_input("subject");
$from = get_input("from");
$description = get_input("description");
$access_id = (int) get_input("access_id");
$tags = string_to_tag_array(get_input("tags"));

$forward_url = REFERER;

$new_entity = false;

if (empty($title)) {
	register_error(elgg_echo("newsletter:action:edit:error:title"));
	forward(REFERER);
}

if (newsletter_custom_from_enabled() && !newsletter_validate_custom_from($from)) {
	register_error(elgg_echo("newsletter:action:edit:error:from"));
	forward(REFERER);
}

if (!empty($guid)) {
	$entity = get_entity($guid);
	
	if (!empty($entity) && $entity->canEdit()) {
		if (!elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
			forward(REFERER);
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
		forward(REFERER);
	}
} else {
	$entity = new Newsletter();
	$entity->owner_guid = $container_guid;
	$entity->container_guid = $container_guid;
	$entity->access_id = $access_id;
	
	$entity->status = "concept";
	
	$new_entity = true;
	
	if (!$entity->save()) {
		register_error(elgg_echo("IOException:UnableToSaveNew", array(elgg_echo("item:object:" . Newsletter::SUBTYPE))));
		forward(REFERER);
	}
}

if (empty($entity)) {
	register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	forward(REFERER);
}

$entity->title = $title;
$entity->description = $description;
$entity->access_id = $access_id;

if (!empty($subject)) {
	$entity->subject = $subject;
} else {
	unset($entity->subject);
}

if (newsletter_custom_from_enabled()) {
	if (!empty($from)) {
		$entity->from = $from;
	} else {
		unset($entity->from);
	}
}

$entity->tags = $tags;

if ($entity->save()) {
	elgg_clear_sticky_form("newsletter_edit");
	
	if ($new_entity) {
		$forward_url = "newsletter/edit/" . $entity->getGUID() . "/template";
	}
	
	system_message(elgg_echo("newsletter:action:edit:success"));
} else {
	register_error(elgg_echo("newsletter:action:edit:error:save"));
}

forward($forward_url);