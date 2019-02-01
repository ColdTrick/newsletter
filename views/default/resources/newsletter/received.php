<?php
/**
 * Shows all the newsletters the user received
 */

use Elgg\EntityNotFoundException;
use Elgg\EntityPermissionsException;

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser) {
	throw new EntityNotFoundException();
}

if (!$user->canEdit()) {
	throw new EntityPermissionsException();
}

// breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE);

// build page elements
if ($user->getGUID() == elgg_get_logged_in_user_guid()) {
	$title_text = elgg_echo('newsletter:received:title:mine');
} else {
	$title_text = elgg_echo('newsletter:received:title', [$user->getDisplayName()]);
}

$content = elgg_call(ELGG_IGNORE_ACCESS, function() use ($user) {
	return elgg_list_entities([
		'type' => 'object',
		'subtype' => Newsletter::SUBTYPE,
		'full_view' => false,
		'metadata_name_value_pairs' => [
			'name' => 'status',
			'value' => 'sent',
		],
		'relationship' => Newsletter::SEND_TO,
		'relationship_guid' => $user->guid,
		'inverse_relationship' => true,
		'order_by_metadata' => [
			'name' => 'start_time',
			'as' => 'integer',
			'direction' => 'DESC',
		],
		'no_results' => true,
	]);
});

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
]);

// draw page
echo elgg_view_page($title_text, $page_data);
