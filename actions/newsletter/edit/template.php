<?php

$guid = (int) get_input('guid');

$html = get_input('html', '', false);
$css = strip_tags(get_input('css', ''));
$template = get_input('template');

if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

/** @var \Newsletter $entity */
$entity = elgg_entity_gatekeeper($guid, 'object', \Newsletter::SUBTYPE, true);

$forward_url = empty($entity->template) ? elgg_generate_entity_url($entity, 'edit', 'content') : REFERRER;

$entity->html = $html;
$entity->css = $css;
$entity->template = $template;

return elgg_ok_response('', elgg_echo('newsletter:action:template:success'), $forward_url);
