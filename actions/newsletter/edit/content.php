<?php
$guid = (int) get_input('guid');
$content = get_input('content', '', false);

if (empty($guid) || empty($content)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$forward_url = REFERER;
if (empty($entity->content)) {
	$forward_url = 'newsletter/edit/' . $entity->getGUID() . '/recipients';
}

$entity->content = $content;

return elgg_ok_response('', elgg_echo('newsletter:action:content:success'), $forward_url);
