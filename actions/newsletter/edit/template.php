<?php

$guid = (int) get_input('guid');

$html = get_input('html', '', false);
$css = strip_tags(get_input('css'));
$template = get_input('template');

if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof Newsletter || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$forward_url = REFERER;
if (empty($entity->template)) {
	$forward_url = elgg_generate_entity_url($entity, 'edit', 'content');
}

$entity->html = $html;
$entity->css = $css;
$entity->template = $template;

return elgg_ok_response('', elgg_echo('newsletter:action:template:success'), $forward_url);
