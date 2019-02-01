<?php
/**
 * List subscriptions to groups the user is not a member of
 *
 * @uses $vars['user'] The user to check for
 */

use Elgg\Database\QueryBuilder;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggUser) {
	return;
}

$group_options = [
	'type' => 'group',
	'relationship' => NewsletterSubscription::SUBSCRIPTION,
	'relationship_guid' => $entity->guid,
	'count' => true,
	'limit' => false,
	'batch' => true,
	'order_by_metadata' => [
		'name' => 'name',
		'direction' => 'ASC',
	],
	'wheres' => [
		function (QueryBuilder $qb, $main_alias) use ($entity) {
			$my_groups = $qb->subquery('entity_relationships');
			$my_groups->select('guid_two')
				->where($qb->compare('relationship', '=', 'member', ELGG_VALUE_STRING))
				->andWhere($qb->compare('guid_one', '=', $entity->guid, ELGG_VALUE_GUID));
			
			return $qb->compare("{$main_alias}.guid", 'not in', $my_groups->getSQL());
		}
	],
];

$other_count = elgg_get_entities($group_options);
if (empty($other_count)) {
	return;
}

unset($group_options['count']);

$content = [];
$content[] = elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:subscriptions:other:description'),
]);

$subscriptions = elgg_get_entities($group_options);

foreach ($subscriptions as $subscription) {
	$has_subscription = newsletter_check_user_subscription($entity, $subscription);
	
	$content[] = elgg_view_field([
		'#type' => 'checkbox',
		'#label' => $subscription->getDisplayName(),
		'name' => "subscriptions[{$subscription->guid}]",
		'value' => 1,
		'checked' => $has_subscription,
		'switch' => true,
	]);
}

echo elgg_view_module('info', elgg_echo('newsletter:subscriptions:other:title'), implode(PHP_EOL, $content));
