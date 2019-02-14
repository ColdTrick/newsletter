<?php
/**
 * View all the site newsletters
 */

$filter = 'sent';

// breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE);

// register title button
newsletter_register_title_menu_items(elgg_get_site_entity());

if (elgg_is_admin_logged_in()) {
	elgg_register_title_button('newsletter', 'add', 'object', Newsletter::SUBTYPE);
	
	$filter = elgg_extract('filter', $vars, $filter, false);
}

// build page elements
$title_text = elgg_echo('newsletter:site:title');

$options = [
	'type' => 'object',
	'subtype' => Newsletter::SUBTYPE,
	'container_guid' => elgg_get_site_entity()->guid,
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
			'direction' => 'ASC',
		];
		break;
	case 'sending':
		$options['metadata_name_value_pairs'] = [
			'name' => 'status',
			'value' => 'sending',
		];
		break;
	case 'sent':
	default:
		$options['metadata_name_value_pairs'] = [];
		$options['metadata_name_value_pairs'][] = [
			'name' => 'status',
			'value' => 'sent',
		];
		if (!elgg_is_admin_logged_in()) {
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
	'filter_id' => 'newsletter',
	'filter_value' => $filter,
]);

// draw page
echo elgg_view_page($title_text, $page_data);
