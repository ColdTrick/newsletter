<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \Newsletter) {
	return;
}

elgg_require_css('forms/newsletter/edit/content');

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view('output/text', ['value' => elgg_echo('newsletter:edit:content:description')]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#help' => elgg_view('newsletter/placeholders'),
	'name' => 'content',
	'value' => elgg_extract('content', $vars),
	'editor_type' => 'newsletter',
]);

if (elgg_extract('upload_header', $vars, true)) {
	echo elgg_view('output/text', ['value' => elgg_echo('newsletter:edit:content:header')]);
	
	echo elgg_view('entity/edit/header', [
		'entity' => $entity,
		'entity_type' => 'object',
		'entity_subtype' => 'newsletter',
		'cropper_aspect_ratio_size' => 'newsletter',
	]);
}

$footer = elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
