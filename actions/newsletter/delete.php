<?php

$guid = (int) get_input('guid');

if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$entity_url = $entity->getURL();
$container = $entity->getContainerEntity();

if (!$entity->delete()) {
	return elgg_error_response(elgg_echo('newsletter:action:delete:error:delete'));
}

$forward_url = REFERER;
// check if we don't forward to the entity url
if ($_SERVER['HTTP_REFERER'] == $entity_url) {
	if (elgg_instanceof($container, 'group')) {
		$forward_url = 'newsletter/group/' . $container->getGUID();
	} else {
		$forward_url = 'newsletter/site';
	}
}

return elgg_ok_response('', elgg_echo('newsletter:action:delete:success'), $forward_url);
