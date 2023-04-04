<?php

$format_description = elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('newsletter:embed:format:description:title'),
	'options_values' => [
		'full' => elgg_echo('newsletter:embed:format:description:option:full'),
		'excerpt' => elgg_echo('newsletter:embed:format:description:option:excerpt'),
		'no' => elgg_echo('newsletter:embed:format:description:option:no'),
	],
	'name' => 'newsletter-embed-format-description',
	'id' => 'newsletter-embed-format-description',
]);

$format_icon = elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:embed:format:icon:title'),
	'name' => 'newsletter-embed-format-icon',
	'id' => 'newsletter-embed-format-icon',
	'value' => 1,
	'checked' => true,
	'switch' => true,
]);

$result = elgg_format_element('fieldset', [], $format_description . $format_icon);

$result .= elgg_view('input/button', [
	'class' => 'elgg-button-submit mvl newsletter-embed-format',
	'value' => elgg_echo('newsletter:embed:format:add_to_newsletter'),
]);

$preview_title = elgg_echo('newsletter:embed:format:preview:title');
$preview_content = elgg_format_element('div', ['id' => 'newsletter-embed-format-preview']);

$result .= elgg_view_module('info', $preview_title, $preview_content);

echo elgg_format_element('div', ['class' => 'hidden', 'id' => 'newsletter-embed-format'], $result);
