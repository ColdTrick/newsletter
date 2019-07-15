<?php
/**
 * Create a new newsletter
 *
 * @uses elgg_get_page_owner_entity() the container in which to create the newsletter
 */

use Elgg\EntityPermissionsException;
use ColdTrick\Newsletter\EditForm;
use Elgg\BadRequestException;

$container = elgg_get_page_owner_entity();

// check if we have access
if ($container instanceof ElggUser) {
	// access to site newsletters is only for admins
	if ($container->isAdmin()) {
		$container = elgg_get_site_entity();
		elgg_set_page_owner_guid(false);
	}
}

if (!$container instanceof ElggEntity) {
	throw new BadRequestException();
}

if (!$container->canWriteToContainer(0, 'object', Newsletter::SUBTYPE)) {
	throw new EntityPermissionsException();
}

// breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $container instanceof ElggGroup ? $container : null);

// build page elements
$title_text = elgg_echo('newsletter:add:title');

$form = new EditForm(null, $container->guid);

$content = elgg_view_form('newsletter/edit', [], $form('basic'));

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
	'filter_id' => 'newsletter_steps',
	'filter_value' => 'basic',
]);

// draw page
echo elgg_view_page($title_text, $page_data);
