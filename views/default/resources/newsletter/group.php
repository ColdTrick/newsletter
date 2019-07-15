<?php
/**
 * List all the newsletter in this group
 *
 * @uses elgg_get_page_owner_entity() the group to list the newsletter from
 */

use Elgg\EntityNotFoundException;

$page_owner = elgg_get_page_owner_entity();
if (!$page_owner instanceof ElggGroup) {
	throw new EntityNotFoundException();
}

elgg_entity_gatekeeper($page_owner->guid, 'group');
elgg_group_tool_gatekeeper('newsletter');

// build breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $page_owner);

// build page elements
newsletter_register_title_menu_items($page_owner);

$filter = 'sent';
if ($page_owner->canWriteToContainer(0, 'object', Newsletter::SUBTYPE)) {
	elgg_register_title_button('newsletter', 'add', 'object', Newsletter::SUBTYPE);
	
	$filter = elgg_extract('filter', $vars, $filter, false);
}

$title_text = elgg_echo('newsletter:group:title', [$page_owner->getDisplayName()]);

$options = [
	'type' => 'object',
	'subtype' => Newsletter::SUBTYPE,
	'container_guid' => $page_owner->guid,
	'full_view' => false,
	'no_results' => true,
];

switch ($filter) {
	case 'concept':
		$options['metadata_name_value_pairs'] = [
			'name' => 'status',
			'value' => 'concept',
		];
		break;
	case 'scheduled':
		$options['metadata_name_value_pairs'] = [
			'name' => 'status',
			'value' => 'scheduled',
		];
		$options['order_by_metadata'] = [
			'name' => 'scheduled',
			'as' => 'integer',
		];
		break;
	case 'sending':
		$options['metadata_name_value_pairs'] = [
			'name' => 'status',
			'value' => 'sending',
		];
		break;
	default:
		$options['metadata_name_value_pairs'] = [];
		$options['metadata_name_value_pairs'][] = [
			'name' => 'status',
			'value' => 'sent',
		];
		if (!$page_owner->canEdit()) {
			$options['metadata_name_value_pairs'][] = [
				'name' => 'show_in_archive',
				'value' => 1,
			];
		}
		
		$options['order_by_metadata'] = [
			'name' => 'start_time',
			'as' => 'integer',
			'direction' => 'DESC',
		];
		break;
}

$content = elgg_list_entities($options);

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
	'filter_id' => 'newsletter/group',
	'filter_value' => $filter,
]);

// draw page
echo elgg_view_page($title_text, $page_data);
