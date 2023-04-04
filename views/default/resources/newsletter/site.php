<?php
/**
 * View all the site newsletters
 */

$filter = 'sent';

elgg_push_collection_breadcrumbs('object', \Newsletter::SUBTYPE);

$site = elgg_get_site_entity();
elgg_set_page_owner_guid($site->guid);

newsletter_register_title_menu_items($site);

if ($site->canWriteToContainer(0, 'object', \Newsletter::SUBTYPE)) {
	elgg_register_title_button('add', 'object', \Newsletter::SUBTYPE);
	
	$filter = elgg_extract('filter', $vars, $filter, false);
}

$options = [
	'type' => 'object',
	'subtype' => \Newsletter::SUBTYPE,
	'container_guid' => $site->guid,
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
			'direction' => 'ASC',
			'signed' => true,
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
		
		$options['sort_by'] = [
			'property' => 'start_time',
			'direction' => 'DESC',
			'signed' => true,
		];
		break;
}

echo elgg_view_page(elgg_echo('newsletter:site:title'), [
	'content' => elgg_list_entities($options),
	'filter_id' => 'newsletter',
	'filter_value' => $filter,
]);
