<?php
/**
 * Create a new newsletter
 *
 * @uses elgg_get_page_owner_entity() the container in which to create the newsletter
 */

use Elgg\EntityPermissionsException;
use ColdTrick\Newsletter\EditForm;

$page_owner = elgg_get_page_owner_entity();
$container_guid = 0;

// check if we have access
if ($page_owner instanceof ElggUser) {
	// access to site newsletters is only for admins
	if ($page_owner->isAdmin()) {
		$container_guid = elgg_get_site_entity()->guid;
	} else {
		throw new EntityPermissionsException();
	}
} elseif ($page_owner instanceof ElggGroup) {
	// only for group owners/admins
	if (newsletter_is_group_enabled($page_owner) && $page_owner->canEdit()) {
		$container_guid = $page_owner->guid;
	} else {
		throw new EntityPermissionsException();
	}
} else {
	throw new EntityPermissionsException();
}

// breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $page_owner instanceof ElggGroup ? $page_owner : null);

// build page elements
$title_text = elgg_echo('newsletter:add:title');

$form = new EditForm(null, $container_guid);

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
