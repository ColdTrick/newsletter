<?php

$plugin = elgg_extract('entity', $vars);

$noyes_options = [
	'no' => elgg_echo('option:no'),
	'yes' => elgg_echo('option:yes'),
];

$url_postfix_replacements = ['{guid}'];

$setting = elgg_echo('newsletter:settings:allow_groups');
$setting .= elgg_view('input/select', [
	'name' => 'params[allow_groups]',
	'value' => $plugin->allow_groups,
	'options_values' => $noyes_options,
	'class' => 'mls',
]);
$setting .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:settings:allow_groups:description'));
echo elgg_format_element('div', [], $setting);

$setting = elgg_echo('newsletter:settings:include_existing_users');
$setting .= elgg_view('input/select', [
	'name' => 'params[include_existing_users]',
	'value' => $plugin->include_existing_users,
	'options_values' => array_reverse($noyes_options),
	'class' => 'mls',
]);
$setting .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:settings:include_existing_users:description'));
echo elgg_format_element('div', [], $setting);

$setting = elgg_echo('newsletter:settings:custom_from');
$setting .= elgg_view('input/select', [
	'name' => 'params[custom_from]',
	'value' => $plugin->custom_from,
	'options_values' => $noyes_options,
	'class' => 'mls',
]);
$setting .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:settings:custom_from:description'));
echo elgg_format_element('div', [], $setting);

$setting = elgg_echo('newsletter:settings:custom_from:domains');
$setting .= elgg_view('input/text', [
	'name' => 'params[custom_from_domains]',
	'value' => $plugin->custom_from_domains,
]);
$setting .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:settings:custom_from:domains:description'));
echo elgg_format_element('div', [], $setting);

// url postfix
$url_postfix = elgg_view('output/longtext', ['value' => elgg_echo('newsletter:settings:url_postfix:description')]);

if ($plugin->url_postfix_name || $plugin->url_postfix_value) {
	$url_postfix .= elgg_format_element('div', ['class' => 'elgg-message elgg-state-error'], elgg_echo('newsletter:settings:url_postfix:upgrade'));
}

$url_postfix .= '<div>';
$url_postfix .= elgg_echo('newsletter:settings:url_postfix:setting');
$url_postfix .= elgg_view('input/plaintext', [
	'name' => 'params[url_postfix]',
	'value' => $plugin->url_postfix,
]);
$url_postfix .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:settings:url_postfix:setting:description', [implode(' ', $url_postfix_replacements)]));
$url_postfix .= '</div>';

echo elgg_view_module('inline', elgg_echo('newsletter:settings:url_postfix'), $url_postfix);
