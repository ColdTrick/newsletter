<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Newsletter) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view('output/text', ['value' => elgg_echo('newsletter:edit:content:description')]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#help' => elgg_view('newsletter/placeholders'),
	'id' => "newsletter-edit-content-{$entity->guid}",
	'name' => 'content',
	'value' => elgg_extract('content', $vars),
]);

if (elgg_extract('upload_header', $vars, true)) {
	echo elgg_view('output/text', ['value' => elgg_echo('newsletter:edit:content:header')]);
	
	echo elgg_view('entity/edit/icon', [
		'entity' => $entity,
		'entity_type' => 'object',
		'entity_subtype' => 'newsletter',
		'cropper_aspect_ratio_size' => 'newsletter_header',
	]);
}

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
