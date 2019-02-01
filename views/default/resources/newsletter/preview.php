<?php

use Elgg\EntityPermissionsException;

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
/* @var $entity Newsletter */
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

$newsletter_content = elgg_view_layout('newsletter', [
	'entity' => $entity,
]);
echo newsletter_apply_url_postfix($newsletter_content, $entity);

echo elgg_view('newsletter/buttons', [
	'entity' => $entity,
	'type' => 'preview',
]);
