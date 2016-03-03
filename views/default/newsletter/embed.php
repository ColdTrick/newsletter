<?php

$newsletter = elgg_extract('entity', $vars);
$offset = (int) max(get_input('offset', 0), 0);
$limit = 6;

$query = get_input('q');
$query = sanitise_string($query);

$dbprefix = elgg_get_config('dbprefix');

$show_all = (bool) get_input('show_all', false);

$subtypes = [];
if (elgg_is_active_plugin('blog')) {
	$subtypes[] = 'blog';
}
if (elgg_is_active_plugin('static')) {
	$subtypes[] = 'static';
}

if (empty($subtypes)) {
	return;
}

$options = [
	'type' => 'object',
	'subtypes' => $subtypes,
	'full_view' => false,
	'limit' => $limit,
	'offset' => $offset,
	'count' => true,
	'wheres' => [],
];

$container = $newsletter->getContainerEntity();
if (empty($show_all) && elgg_instanceof($container, 'group')) {
	$container_guid = $newsletter->getContainerGUID();
	
	if (elgg_is_active_plugin('static')) {
		// static subpages do not have a group container so do an extra check
		$options['wheres'][] = "
			((e.container_guid = {$container_guid}) OR e.guid IN (
				SELECT sub_r.guid_one from {$dbprefix}entity_relationships sub_r
				JOIN {$dbprefix}entities sub_e ON sub_e.guid = sub_r.guid_two
				WHERE sub_e.container_guid = {$container_guid}
				AND sub_r.relationship = 'subpage_of'
			))";
	} else {
		$options['container_guid'] = $container_guid;
	}
}

if (!empty($query)) {
	$options['joins'] = ["JOIN {$dbprefix}objects_entity oe ON e.guid = oe.guid"];
	$options['wheres'][] = "(oe.title LIKE '%{$query}%')";
}

$count = elgg_get_entities($options);
unset($options['count']);

// search form
$form_data = elgg_view('input/text', ['name' => 'q', 'value' => $query]);
$form_data .= elgg_view('input/submit', ['value' => elgg_echo('search'), 'class' => 'elgg-button-action']);

if (elgg_instanceof($container, 'group')) {
	$show_all_checkbox = elgg_view('input/checkbox', [
		'name' => 'show_all',
		'value' => '1',
		'checked' => $show_all,
		'default' => false,
	]);
	$show_all_checkbox .= elgg_echo('newsletter:embed:show_all');
	$form_data .= elgg_format_element('div', ['class' => 'mts'], $show_all_checkbox);
}

$embed_wrapper = elgg_view('input/form', [
	'action' => 'newsletter/embed/' . $newsletter->getGUID(),
	'id' => 'newsletter-embed-search',
	'body' => $form_data,
	'disable_security' => true,
]);

if ($count > 0) {
	$entities = elgg_get_entities($options);
	
	$embed_list = '';
	foreach ($entities as $entity) {
		$embed_list .= elgg_format_element('li', ['class' => 'newsletter-embed-item'], newsletter_view_embed_content($entity, ['newsletter' => $newsletter]));
	}
	
	$embed_wrapper .= elgg_format_element('ul', ['id' => 'newsletter-embed-list'], $embed_list);
	
	$show_all_value = $show_all ? 1 : 0;
	
	$embed_wrapper_pagination = elgg_view('navigation/pagination', [
		'base_url' => elgg_normalize_url("newsletter/embed/{$newsletter->getGUID()}?q={$query}&show_all={$show_all_value}"),
		'offset' => $offset,
		'limit' => $limit,
		'count' => $count,
	]);
	$embed_wrapper .= elgg_format_element('div', ['id' => 'newsletter-embed-pagination'], $embed_wrapper_pagination);
} else {
	$embed_wrapper .= elgg_echo('notfound');
}

echo elgg_format_element('div', ['id' => 'newsletter-embed-wrapper'], $embed_wrapper);
if ($count > 0) {
	echo elgg_view('newsletter/format');
}
