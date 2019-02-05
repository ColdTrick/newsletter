<?php

$format_description = elgg_format_element('label', [], elgg_echo('newsletter:embed:format:description:title'));
$format_description .= elgg_view('input/select', [
	'options_values' => [
		'full' => elgg_echo('newsletter:embed:format:description:option:full'),
		'excerpt' => elgg_echo('newsletter:embed:format:description:option:excerpt'),
		'no' => elgg_echo('newsletter:embed:format:description:option:no'),
	],
	'name' => 'newsletter-embed-format-description',
	'id' => 'newsletter-embed-format-description',
	'class' => 'mlm',
]);
$format_description = elgg_format_element('div', [], $format_description);

$format_icon = elgg_format_element('label', [], elgg_echo('newsletter:embed:format:icon:title'));
$format_icon .= elgg_view('input/select', [
	'options_values' => [
		'left' => elgg_echo('newsletter:embed:format:icon:option:left'),
		'right' => elgg_echo('newsletter:embed:format:icon:option:right'),
		'none' => elgg_echo('newsletter:embed:format:icon:option:none'),
	],
	'name' => 'newsletter-embed-format-icon',
	'id' => 'newsletter-embed-format-icon',
	'class' => 'mlm',
]);

$format_icon = elgg_format_element('div', [], $format_icon);

$result = elgg_format_element('fieldset', [], $format_description . $format_icon);

$result .= elgg_view('input/button', [
	'class' => 'elgg-button-submit mvl newsletter-embed-format',
	'value' => elgg_echo('newsletter:embed:format:add_to_newsletter'),
]);

$preview_title = elgg_echo('newsletter:embed:format:preview:title');
$preview_content = elgg_format_element('div', ['id' => 'newsletter-embed-format-preview']);

$result .= elgg_view_module('info', $preview_title, $preview_content);

echo elgg_format_element('div', ['class' => 'hidden', 'id' => 'newsletter-embed-format'], $result);
