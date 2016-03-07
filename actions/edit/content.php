<?php
$guid = (int) get_input('guid');
$content = get_input('content', '', false);

if (empty($guid) || empty($content)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

$forward_url = REFERER;
if (empty($entity->content)) {
	$forward_url = 'newsletter/edit/' . $entity->getGUID() . '/recipients';
}

$entity->content = $content;

// some cleanup
system_message(elgg_echo('newsletter:action:content:success'));

forward($forward_url);