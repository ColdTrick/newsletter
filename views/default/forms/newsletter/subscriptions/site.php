<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggUser) {
	return;
}

$site = elgg_get_site_entity();

if (elgg_get_plugin_setting('allow_site', 'newsletter') === 'yes') {
	// site subscription
	$has_subscription = newsletter_check_user_subscription($entity, $site);
	echo elgg_view_field([
		'#type' => 'checkbox',
		'#label' => elgg_echo('newsletter:subscriptions:site'),
		'name' => "subscriptions[{$site->guid}]",
		'value' => 1,
		'checked' => $has_subscription,
		'switch' => true,
	]);
}

// block all
echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:unsubscribe:all', [$site->getDisplayName()]),
	'name' => 'block_all',
	'value' => 1,
	'checked' => $entity->hasRelationship($site->guid, \NewsletterSubscription::GENERAL_BLACKLIST),
	'switch' => true,
]);
