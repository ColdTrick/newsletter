<?php

$entity = elgg_extract('entity', $vars);
$site = elgg_get_site_entity();

$header_row = elgg_format_element('th', [], '&nbsp;');
$header_row .= elgg_format_element('th', ['class' => 'newsletter-settings-small'], elgg_echo('on'));
$header_row .= elgg_format_element('th', ['class' => 'newsletter-settings-small'], elgg_echo('off'));
$rows = elgg_format_element('tr', [], $header_row);

$has_subscription = newsletter_check_user_subscription($entity, $site);

$row_data = elgg_format_element('td', [], $site->name);
$row_data .= elgg_format_element('td', ['class' => 'newsletter-settings-small'], elgg_format_element('input',[
	'type' => 'radio',
	'name' => "subscriptions[{$site->getGUID()}]",
	'value' => '1',
	'checked' => $has_subscription,
]));

$row_data .= elgg_format_element('td', ['class' => 'newsletter-settings-small'], elgg_format_element('input',[
	'type' => 'radio',
	'name' => "subscriptions[{$site->getGUID()}]",
	'value' => '0',
	'checked' => !$has_subscription,
]));
$rows .= elgg_format_element('tr', [], $row_data);

$black_list_relationship = check_entity_relationship($entity->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID());
$block_all = elgg_view('input/checkbox', [
	'name' => 'block_all',
	'value' => '1',
	'id' => 'newsletter-subscription-block-all',
	'checked' => !empty($black_list_relationship),
]);
$block_all .= elgg_format_element('label', ['for' => 'newsletter-subscription-block-all'], elgg_echo('newsletter:unsubscribe:all', [$site->name]));

$content = elgg_view('output/longtext', ['value' => elgg_echo('newsletter:subscriptions:site:description'), 'class' => 'mtn mbs']);
$content .= elgg_format_element('table', ['class' => 'elgg-table-alt'], $rows);
$content .= elgg_format_element('div', ['class' => 'mtm'], $block_all);

echo elgg_view_module('info', elgg_echo('newsletter:subscriptions:site:title'), $content);