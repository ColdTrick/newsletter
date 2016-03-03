<?php
$guid = (int) get_input('guid');
$content = get_input('content', '', false);

if (empty($guid) || empty($content)) {
	register_error(elgg_echo('InvalidParameterException:MissingParameter'));
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	register_error(elgg_echo('InvalidParameterException:NoEntityFound'));
	forward(REFERER);
}

if (!elgg_instanceof($entity, 'object', Newsletter::SUBTYPE)) {
	register_error(elgg_echo('ClassException:ClassnameNotClass', [$guid, elgg_echo('item:object:' . Newsletter::SUBTYPE)]));
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