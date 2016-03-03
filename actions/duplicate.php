<?php
/**
 * Duplicate a newsletter for later use
 */

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

if (!elgg_instanceof($entity, 'object', Newsletter::SUBTYPE)) {
	register_error(elgg_echo('ClassException:ClassnameNotClass', [$guid, elgg_echo('item:object:' . Newsletter::SUBTYPE)]));
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
