<?php
/**
 * View the logging of a newsletter
 *
 * @uses get_input("guid") the GUID of the newsletter to view
 */

$guid = (int) get_input('guid');
elgg_entity_gatekeeper($guid, 'object', \Newsletter::SUBTYPE, true);

/* @var $entity \Newsletter */
$entity = get_entity($guid);

$container = $entity->getContainerEntity();
if ($container instanceof \ElggGroup) {
	elgg_set_page_owner_guid($entity->container_guid);
}

elgg_push_entity_breadcrumbs($entity);

echo elgg_view_page(elgg_echo('newsletter:log:title', [$entity->getDisplayName()]), [
	'content' => elgg_view('newsletter/log', [
		'entity' => $entity,
	]),
	'filter' => false,
]);
