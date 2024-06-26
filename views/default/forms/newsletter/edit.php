<?php

$entity = elgg_extract('entity', $vars);
if ($entity instanceof \Newsletter) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'guid',
		'value' => $entity->guid,
	]);
}

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars),
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('newsletter:edit:subject'),
	'#help' => elgg_echo('newsletter:edit:subject:help'),
	'name' => 'subject',
	'value' => elgg_extract('subject', $vars),
]);

if (elgg_get_plugin_setting('custom_from', 'newsletter') === 'yes') {
	echo elgg_view_field([
		'#type' => 'email',
		'#label' => elgg_echo('newsletter:edit:from'),
		'#help' => elgg_echo('newsletter:edit:from:description', [elgg_format_element('strong', [], elgg_get_site_entity()->getEmailAddress())]),
		'name' => 'from',
		'value' => elgg_extract('from', $vars),
	]);
}

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('description'),
	'#help' => elgg_echo('newsletter:edit:description:description'),
	'name' => 'description',
	'value' => elgg_extract('description', $vars),
]);

echo elgg_view_field([
	'#type' => 'tags',
	'#label' => elgg_echo('tags'),
	'name' => 'tags',
	'value' => elgg_extract('tags', $vars),
]);

echo elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
	'value' => elgg_extract('access_id', $vars),
]);

echo elgg_view_field([
	'#type' => 'container_guid',
	'entity_type' => 'object',
	'entity_subtype' => \Newsletter::SUBTYPE,
	'value' => elgg_extract('container_guid', $vars),
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
