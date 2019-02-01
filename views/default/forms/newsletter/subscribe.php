<?php
/**
 * Form to (un)subscribe to the newsletter of a container entity
 *
 * @uses $vars['entity'] The container entity to (un)subscribe to
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

$user = elgg_get_logged_in_user_entity();
$submit_text = elgg_echo('newsletter:subscribe');

if ($user instanceof \ElggUser) {
	// check if the user is subscribed to container
	if (newsletter_check_user_subscription($user, $entity)) {
		// already subscribed, so offer unsubscibe
		$submit_text = elgg_echo('newsletter:unsubscribe');
		
		echo elgg_format_element('div', [], elgg_echo('newsletter:subscribe:user:description:unsubscribe', [$entity->getDisplayName()]));
	} else {
		// not yet so subscribe
		echo elgg_format_element('div', [], elgg_echo('newsletter:subscribe:user:description:subscribe', [$entity->getDisplayName()]));
	}
	
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'user_guid',
		'value' => $user->guid,
	]);
} else {
	// show email subscribe form
	echo elgg_view_field([
		'#type' => 'email',
		'#label' => elgg_echo('newsletter:subscribe:email:description', [$entity->getDisplayName()]),
		'name' => 'email',
		'title' => elgg_echo('newsletter:recipients:email'),
		'placeholder' => elgg_echo('newsletter:recipients:email'),
		'required' => true,
	]);
}

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => $submit_text,
	'class' => 'float-alt',
]);
elgg_set_form_footer($footer);
