<?php
/**
 * Manage group newsletter subscription
 *
 * @uses $vars['entity'] the user to manage for
 */

use Elgg\Database\QueryBuilder;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggUser) {
	return;
}

$group_options = [
	'count' => true,
	'limit' => false,
	'batch' => true,
	'order_by_metadata' => [
		'name' => 'name',
		'direction' => 'ASC',
	],
	'wheres' => [
		function (QueryBuilder $qb, $main_alias) {
			$group_tool = elgg()->group_tools->get('newsletter');
			
			$tool_value = $group_tool->isEnabledByDefault() ? 'no' : 'yes';
			$tool_compare = $group_tool->isEnabledByDefault() ? 'not in' : 'in';
			
			$tool_disabled = $qb->subquery('metadata');
			$tool_disabled->select('entity_guid')
				->where($qb->compare('name', '=', $group_tool->mapMetadataName(), ELGG_VALUE_STRING))
				->andWhere($qb->compare('value', '=', $tool_value, ELGG_VALUE_STRING));
			
			return $qb->compare("{$main_alias}.guid", $tool_compare, $tool_disabled->getSQL());
		},
	],
];

$group_count = $entity->getGroups($group_options);
if (empty($group_count)) {
	return;
}
unset($group_options['count']);

$content = [];

$content[] = elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:subscriptions:groups:description'),
]);

$my_groups = $entity->getGroups($group_options);

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
