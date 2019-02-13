<?php

$entity = elgg_extract('entity', $vars);
$user = elgg_get_logged_in_user_entity();
if (!$entity instanceof Newsletter || !$user instanceof ElggUser) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view_field([
	'#type' => 'email',
	'#label' => elgg_echo('newsletter:recipients:email'),
	'name' => 'email',
	'value' => $user->email,
	'placeholder' => elgg_echo('newsletter:recipients:email'),
	'required' => true,
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('send'),
]);
elgg_set_form_footer($footer);
