<?php
/**
 * View the logging of a newsletter
 *
 * @uses get_input("guid") the GUID of the newsletter to view
 */

use Elgg\Exceptions\Http\EntityPermissionsException;

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);

/* @var $entity Newsletter */
$entity = get_entity($guid);
if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

$container = $entity->getContainerEntity();
if ($container instanceof \ElggGroup) {
	elgg_set_page_owner_guid($entity->container_guid);
}

elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $container instanceof \ElggGroup ? $container : null);
elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());

echo elgg_view_page(elgg_echo('newsletter:log:title', [$entity->getDisplayName()]), [
	'content' => elgg_view('newsletter/log', [
		'entity' => $entity,
	]),
	'filter' => false,
]);
