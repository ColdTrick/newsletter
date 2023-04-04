<?php

elgg_make_sticky_form('newsletter/edit/content');

$guid = (int) get_input('guid');
$content = get_input('content', '', false);

if (empty($guid) || empty($content)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof \Newsletter || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$forward_url = $entity->content ? REFERRER : elgg_generate_entity_url($entity, 'edit', 'recipients');

$entity->content = $content;

if (get_input('header_remove')) {
	$entity->deleteIcon('header');
} else {
	$entity->saveIconFromUploadedFile('header', 'header');
}

elgg_clear_sticky_form('newsletter/edit/content');

return elgg_ok_response('', elgg_echo('newsletter:action:content:success'), $forward_url);
