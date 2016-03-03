<?php

$guid = (int) get_input('guid');

$html = get_input('html', '', false);
$css = strip_tags(get_input('css'));
$template = get_input('template');


if (empty($guid)) {
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
if (empty($entity->template)) {
	$forward_url = 'newsletter/edit/' . $entity->getGUID() . '/content';
}

$entity->html = $html;
$entity->css = $css;
$entity->template = $template;

system_message(elgg_echo('newsletter:action:template:success'));

forward($forward_url);
