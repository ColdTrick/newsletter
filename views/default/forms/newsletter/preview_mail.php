<?php

$entity = elgg_extract('entity', $vars);
$user = elgg_get_logged_in_user_entity();
if (empty($user) || empty($entity)) {
	return;
}
echo elgg_view('input/email', [
	'name' => 'email',
	'value' => $user->email,
	'placeholder' => elgg_echo('newsletter:recipients:email'),
]);
echo elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);
echo elgg_view('input/submit', ['value' => elgg_echo('send')]);