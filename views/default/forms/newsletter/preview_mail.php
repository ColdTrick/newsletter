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
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'email',
			'name' => 'email',
			'value' => $user->email,
			'placeholder' => elgg_echo('newsletter:recipients:email'),
		],
		[
			'#type' => 'submit',
			'value' => elgg_echo('send'),
		],
	],
]);
