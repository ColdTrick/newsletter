<?php

$guid = (int) get_input('guid');
if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof NewsletterTemplate || !$entity->canDelete()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$entity_url = $entity->getURL();
$container = $entity->getContainerEntity();

if (!$entity->delete()) {
	return elgg_error_response(elgg_echo('newsletter:action:template:delete:error:delete'));
}

$forward_url = REFERER;
// check if we don't forward to the entity url
if ($_SERVER['HTTP_REFERER'] == $entity_url) {
	if ($container instanceof ElggGroup) {
		$forward_url = elgg_generate_url('collection:object:newsletter:group', [
			'guid' => $container->guid,
		]);
	} else {
		$forward_url = elgg_generate_url('collection:object:newsletter:site');
	}
}

return elgg_ok_response('', elgg_echo('newsletter:action:template:delete:success'), $forward_url);
