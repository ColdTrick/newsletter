<?php

$guid = (int) get_input('guid');

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

$entity_url = $entity->getURL();
$container = $entity->getContainerEntity();

if (!$entity->delete()) {
	register_error(elgg_echo('newsletter:action:delete:error:delete'));
	forward(REFERER);
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

system_message(elgg_echo('newsletter:action:delete:success'));

forward($forward_url);