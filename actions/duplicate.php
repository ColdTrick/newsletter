<?php
/**
 * Duplicate a newsletter for later use
 */

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
			
$clone = clone $entity;
if (!$clone->save()) {
	register_error(elgg_echo('newsletter:action:duplicate:error'));
	forward(REFERER);
}

system_message(elgg_echo('newsletter:action:duplicate:success'));
// forward to the edit page so you can start working with the clone
forward('newsletter/edit/' . $clone->getGUID());
