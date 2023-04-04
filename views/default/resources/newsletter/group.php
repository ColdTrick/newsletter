<?php
/**
 * List all the newsletter in this group
 *
 * @uses elgg_get_page_owner_entity() the group to list the newsletter from
 */

$page_owner = elgg_get_page_owner_entity();

elgg_group_tool_gatekeeper('newsletter');

elgg_push_collection_breadcrumbs('object', \Newsletter::SUBTYPE, $page_owner);

newsletter_register_title_menu_items($page_owner);

$filter = 'sent';
if ($page_owner->canWriteToContainer(0, 'object', \Newsletter::SUBTYPE)) {
	elgg_register_title_button('add', 'object', \Newsletter::SUBTYPE);
	
	$filter = elgg_extract('filter', $vars, $filter, false);
}

$options = [
	'type' => 'object',
	'subtype' => \Newsletter::SUBTYPE,
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
		$options['sort_by'] = [
			'property' => 'scheduled',
			'signed' => true,
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
		
		$options['sort_by'] = [
			'property' => 'start_time',
			'direction' => 'DESC',
			'signed' => true,
		];
		break;
}

echo elgg_view_page(elgg_echo('newsletter:group:title', [$page_owner->getDisplayName()]), [
	'content' => elgg_list_entities($options),
	'filter_id' => 'newsletter/group',
	'filter_value' => $filter,
]);
