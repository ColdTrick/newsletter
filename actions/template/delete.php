<?php
$guid = (int) get_input('guid');

if (empty($guid)) {
	register_error(elgg_echo('InvalidParameterException:MissingParameter'));
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	register_error(elgg_echo('InvalidParameterException:NoEntityFound'));
	forward(REFERER);
}

if (!elgg_instanceof($entity, 'object', NEWSLETTER_TEMPLATE)) {
	register_error(elgg_echo('ClassException:ClassnameNotClass', [$guid, elgg_echo('item:object:' . NEWSLETTER_TEMPLATE)]));
	forward(REFERER);
}

$entity_url = $entity->getURL();
$container = $entity->getContainerEntity();

if (!$entity->delete()) {
	register_error(elgg_echo('newsletter:action:template:delete:error:delete'));
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

system_message(elgg_echo('newsletter:action:template:delete:success'));

forward($forward_url);
