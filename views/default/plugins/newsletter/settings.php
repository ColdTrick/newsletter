<?php

/* @var $plugin ElggPlugin */
$plugin = elgg_extract('entity', $vars);

$noyes_options = [
	'no' => elgg_echo('option:no'),
	'yes' => elgg_echo('option:yes'),
];

$url_postfix_replacements = ['{guid}'];

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:settings:allow_groups'),
	'#help' => elgg_echo('newsletter:settings:allow_groups:description'),
	'name' => 'params[allow_groups]',
	'default' => 'no',
	'value' => 'yes',
	'checked' => $plugin->allow_groups === 'yes',
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:settings:include_existing_users'),
	'#help' => elgg_echo('newsletter:settings:include_existing_users:description'),
	'name' => 'params[include_existing_users]',
	'default' => 'no',
	'value' => 'yes',
	'checked' => $plugin->include_existing_users === 'yes',
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:settings:custom_from'),
	'#help' => elgg_echo('newsletter:settings:custom_from:description'),
	'name' => 'params[custom_from]',
	'default' => 'no',
	'value' => 'yes',
	'checked' => $plugin->custom_from === 'yes',
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('newsletter:settings:custom_from:domains'),
	'#help' => elgg_echo('newsletter:settings:custom_from:domains:description'),
	'name' => 'params[custom_from_domains]',
	'value' => $plugin->custom_from_domains,
]);

// url postfix
$url_postfix = elgg_view('output/longtext', ['value' => elgg_echo('newsletter:settings:url_postfix:description')]);
$url_postfix .= elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('newsletter:settings:url_postfix:setting'),
	'#help' => elgg_echo('newsletter:settings:url_postfix:setting:description', [implode(' ', $url_postfix_replacements)]),
	'name' => 'params[url_postfix]',
	'value' => $plugin->url_postfix,
]);

echo elgg_view_module('info', elgg_echo('newsletter:settings:url_postfix'), $url_postfix);
