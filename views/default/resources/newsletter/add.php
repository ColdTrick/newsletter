<?php
/**
 * Create a new newsletter
 *
 * @uses elgg_get_page_owner_entity() the container in which to create the newsletter
 */

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\EntityPermissionsException;

$container = elgg_get_page_owner_entity();

// check if we have access
if ($container instanceof \ElggUser) {
	// access to site newsletters is only for admins
	if ($container->isAdmin()) {
		$container = elgg_get_site_entity();
		elgg_set_page_owner_guid(0);
	}
}

if (!$container instanceof \ElggEntity) {
	throw new BadRequestException();
}

if (!$container->canWriteToContainer(0, 'object', \Newsletter::SUBTYPE)) {
	throw new EntityPermissionsException();
}

// breadcrumb
elgg_push_collection_breadcrumbs('object', \Newsletter::SUBTYPE, $container instanceof \ElggGroup ? $container : null);

$content = elgg_view_form('newsletter/edit', [], ['container_guid' => $container->guid]);

// draw page
echo elgg_view_page(elgg_echo('newsletter:add:title'), [
	'content' => $content,
	'filter_id' => 'newsletter_steps',
	'filter_value' => 'basic',
]);
