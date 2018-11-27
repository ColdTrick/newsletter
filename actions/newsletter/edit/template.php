<?php

$guid = (int) get_input('guid');

$html = get_input('html', '', false);
$css = strip_tags(get_input('css'));
$template = get_input('template');


if (empty($guid)) {
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
if (empty($entity->template)) {
	$forward_url = 'newsletter/edit/' . $entity->getGUID() . '/content';
}

$entity->html = $html;
$entity->css = $css;
$entity->template = $template;

system_message(elgg_echo('newsletter:action:template:success'));

forward($forward_url);
