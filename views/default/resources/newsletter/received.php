<?php
/**
 * Shows all the newsletters the user received
 */

/* @var $user \ElggUser */
$user = elgg_get_page_owner_entity();

elgg_push_collection_breadcrumbs('object', \Newsletter::SUBTYPE);

if ($user->guid == elgg_get_logged_in_user_guid()) {
	$title_text = elgg_echo('newsletter:received:title:mine');
} else {
	$title_text = elgg_echo('newsletter:received:title', [$user->getDisplayName()]);
}

$content = elgg_call(ELGG_IGNORE_ACCESS, function() use ($user) {
	return elgg_list_entities([
		'type' => 'object',
		'subtype' => \Newsletter::SUBTYPE,
		'full_view' => false,
		'metadata_name_value_pairs' => [
			'name' => 'status',
			'value' => 'sent',
		],
		'relationship' => \Newsletter::SEND_TO,
		'relationship_guid' => $user->guid,
		'inverse_relationship' => true,
		'sort_by' => [
			'property' => 'start_time',
			'direction' => 'DESC',
			'signed' => true,
		],
		'no_results' => true,
	]);
});

echo elgg_view_page($title_text, [
	'content' => $content,
	'filter' => false,
]);
