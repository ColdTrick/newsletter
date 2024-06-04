<?php
/**
 * Duplicate a newsletter for later use
 */

$guid = (int) get_input('guid');
if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof \Newsletter || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}
			
$clone = clone $entity;
if (!$clone->save()) {
	return elgg_error_response(elgg_echo('newsletter:action:duplicate:error'));
}

if ($entity->hasIcon('master', 'header')) {
	$coords = $entity->getIconCoordinates('header') ?? [];
	
	$clone->saveIconFromElggFile($entity->getIcon('master', 'header'), 'header', $coords);
}

// forward to the edit page, so you can start working with the clone
return elgg_ok_response('', elgg_echo('newsletter:action:duplicate:success'), elgg_generate_entity_url($clone, 'edit'));
