<?php
/**
 * View the logging of a newsletter
 *
 * @uses get_input("guid") the GUID of the newsletter to view
 */

use Elgg\EntityPermissionsException;

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
/* @var $entity Newsletter */
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

// set page owner
$container = $entity->getContainerEntity();
if ($container instanceof ElggGroup) {
	elgg_set_page_owner_guid($entity->container_guid);
}

// breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $container instanceof ElggGroup ? $container : null);
elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());

// build page elements
$title_text = elgg_echo('newsletter:log:title', [$entity->getDisplayName()]);

$content = elgg_view('newsletter/log', [
	'entity' => $entity,
]);

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
]);

// draw page
echo elgg_view_page($title_text, $page_data);
