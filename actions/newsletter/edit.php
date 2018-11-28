<?php

elgg_make_sticky_form('newsletter/edit');

$guid = (int) get_input('guid');
$container_guid = (int) get_input('container_guid');
$title = get_input('title');
$subject = get_input('subject');
$from = get_input('from');
$description = get_input('description');
$access_id = (int) get_input('access_id');
$tags = string_to_tag_array(get_input('tags'));

$new_entity = false;

if (empty($title)) {
	return elgg_error_response(elgg_echo('newsletter:action:edit:error:title'));
}

if ((elgg_get_plugin_setting('custom_from', 'newsletter') === 'yes') && !newsletter_validate_custom_from($from)) {
	return elgg_error_response(elgg_echo('newsletter:action:edit:error:from'));
}

if (!empty($guid)) {
	$entity = get_entity($guid);
	if (!$entity instanceof Newsletter || !$entity->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} else {
	$entity = new Newsletter();
	$entity->owner_guid = $container_guid;
	$entity->container_guid = $container_guid;
	$entity->access_id = $access_id;
	
	$new_entity = true;
	
	if (!$entity->save()) {
		return elgg_error_response(elgg_echo('save:fail'));
	}
}

$entity->title = $title;
$entity->description = $description;
$entity->access_id = $access_id;

if (!empty($subject)) {
	$entity->subject = $subject;
} else {
	unset($entity->subject);
}

if (elgg_get_plugin_setting('custom_from', 'newsletter') === 'yes') {
	if (!empty($from)) {
		$entity->from = $from;
	} else {
		unset($entity->from);
	}
}

$entity->tags = $tags;

if (!$entity->save()) {
	return elgg_error_response(elgg_echo('newsletter:action:edit:error:save'));
}

elgg_clear_sticky_form('newsletter/edit');

$forward_url = REFERRER;
if ($new_entity) {
	$forward_url = elgg_generate_entity_url($entity, 'edit', 'template');
}

return elgg_ok_response('', elgg_echo('newsletter:action:edit:success'), $forward_url);
