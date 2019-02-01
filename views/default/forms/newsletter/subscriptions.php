<?php
/**
 * Manage the subsciptions of a specific user
 *
 * @uses $vars['entity'] The user to manage the subscriptions of
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggUser) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'user_guid',
	'value' => $entity->guid,
]);

// description
echo elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:subscriptions:description'),
	'class' => 'mtn'
]);

// site subscription
echo elgg_view('forms/newsletter/subscriptions/site', $vars);

// are group newsletters allowed
if (newsletter_is_group_enabled()) {
	// my group subscriptions
	echo elgg_view('forms/newsletter/subscriptions/groups', $vars);
	
	//other group subscriptions
	echo elgg_view('forms/newsletter/subscriptions/other', $vars);
}

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
