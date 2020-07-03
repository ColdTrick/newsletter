<?php

/* @var $newsletter Newsletter */
$newsletter = elgg_extract('entity', $vars);
$offset = (int) max(get_input('offset', 0), 0);
$limit = 6;

$query = get_input('q');
$subtype_filter = get_input('subtype_filter');

$show_all = (bool) get_input('show_all', false);

$allowed_subtypes = get_registered_entity_types('object');
$allowed_subtypes = array_filter($allowed_subtypes, function ($subtype) {
	return (bool) elgg_get_plugin_setting("embed_enable_object_{$subtype}", 'newsletter', 1);
});

	if (empty($allowed_subtypes)) {
	return;
}

$subtype_options = [
	'' => elgg_echo('filter'),
];
foreach ($allowed_subtypes as $subtype) {
	$subtype_options[$subtype] = elgg_echo("item:object:{$subtype}");
}

$options = [
	'type' => 'object',
	'subtypes' => $subtype_filter ?: $allowed_subtypes,
	'full_view' => false,
	'limit' => $limit,
	'offset' => $offset,
	'count' => true,
	'query' => $query,
];

$container = $newsletter->getContainerEntity();
if (empty($show_all) && $container instanceof ElggGroup) {
	$options['container_guid'] = $newsletter->container_guid;
}

if (empty($query)) {
	$count = elgg_get_entities($options);
} else {
	$count = elgg_search($options);
}
unset($options['count']);

// search form
$form_data = elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'name' => 'q',
			'value' => $query,
			'placeholder' => elgg_echo('search'),
		],
		[
			'#type' => 'select',
			'name' => 'subtype_filter',
			'value' => $subtype_filter,
			'options_values' => $subtype_options,
		],
		[
			'#type' => 'submit',
			'value' => elgg_echo('search'),
		],
	],
]);

if ($container instanceof ElggGroup) {
	$form_data .= elgg_view_field([
		'#type' => 'checkbox',
		'#label' => elgg_echo('newsletter:embed:show_all'),
		'name' => 'show_all',
		'value' => '1',
		'checked' => $show_all,
		'default' => false,
		'switch' => true,
	]);
}

$embed_wrapper = elgg_view('input/form', [
	'action' => elgg_generate_url('default:object:newsletter:embed', [
		'guid' => $newsletter->guid,
	]),
	'id' => 'newsletter-embed-search',
	'body' => $form_data,
	'disable_security' => true,
]);

if ($count > 0) {
	if (empty($query)) {
		$entities = elgg_get_entities($options);
	} else {
		$entities = elgg_search($options);
	}
	
	$embed_list = [];
	foreach ($entities as $entity) {
		$entity_embed = newsletter_view_embed_content($entity, ['newsletter' => $newsletter]);
		$embed_list[] = elgg_format_element('li', ['class' => 'newsletter-embed-item'], $entity_embed);
	}
	
	$embed_wrapper .= elgg_format_element('ul', ['id' => 'newsletter-embed-list'], implode(PHP_EOL, $embed_list));
	
	$show_all_value = $show_all ? 1 : 0;
	
	$embed_wrapper_pagination = elgg_view('navigation/pagination', [
		'base_url' => elgg_generate_url('default:object:newsletter:embed', [
			'guid' => $newsletter->guid,
			'q' => $query,
			'show_all' => $show_all_value,
			'subtype_filter' => $subtype_filter,
		]),
		'offset' => $offset,
		'limit' => $limit,
		'count' => $count,
	]);
	$embed_wrapper .= elgg_format_element('div', ['id' => 'newsletter-embed-pagination'], $embed_wrapper_pagination);
} else {
	$embed_wrapper .= elgg_echo('notfound');
}

$formatter = ($count > 0) ? elgg_view('newsletter/format') : '';

echo elgg_format_element('div', ['id' => 'newsletter-embed-wrapper'], $embed_wrapper . $formatter);
