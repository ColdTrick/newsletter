<?php
/**
 * Manage group newsletter subscription
 *
 * @uses $vars['entity'] the user to manage for
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggUser) {
	return;
}

$group_count = $entity->getGroups([
	'count' => true,
]);
if (empty($group_count)) {
	return;
}

$content = [];

$content[] = elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:subscriptions:groups:description'),
]);

$my_groups = $entity->getGroups([
	'limit' => false,
	'batch' => true,
	'order_by_metadata' => [
		'name' => 'name',
		'direction' => 'ASC',
	],
]);

/* @var $group ElggGroup */
foreach ($my_groups as $group) {
	$has_subscription = newsletter_check_user_subscription($entity, $group);
	
	$content[] = elgg_view_field([
		'#type' => 'checkbox',
		'#label' => $group->getDisplayName(),
		'name' => "subscriptions[{$group->guid}]",
		'value' => 1,
		'checked' => $has_subscription,
		'switch' => true,
	]);
}

echo elgg_view_module('info', elgg_echo('newsletter:subscriptions:groups:title'), implode(PHP_EOL, $content));
