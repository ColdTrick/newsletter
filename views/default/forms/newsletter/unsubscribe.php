<?php
/**
 * Form content for the unsubscribe action
 * Users get here by a link in their emailed newsletter
 *
 * @uses $vars['entity'] The container to unsubscribe from (primairly)
 * @uses $vars['recipient'] The recipient of the newsletter (user_guid or email address)
 * @uses $vars['code'] The validation code, to be used in the action
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'entity_guid',
	'value' => $entity->guid,
]);
echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'code',
	'value' => elgg_extract('code', $vars),
]);

$recipient = elgg_extract('recipient', $vars);
if (is_numeric($recipient)) {
	$user = get_user($recipient);
	
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:unsubscribe:user', [$user->getDisplayName()])]);
} elseif (newsletter_is_email_address($recipient)) {
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:unsubscribe:email', [$recipient])]);
} elseif (!$recipient) {
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:unsubscribe:email:empty')]);
	echo elgg_view('input/text', ['name' => 'recipient']) . '<br /><br />';
}
if (!empty($recipient)) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'recipient',
		'value' => $recipient,
	]);
}

// unsubscribe from the provided container
echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:unsubscribe:entity', [$entity->getDisplayName()]),
	'name' => 'guid',
	'value' => $entity->guid,
	'checked' => true,
	'switch' => true,
]);

// unsubscribe from all newsletters
echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:unsubscribe:all', [elgg_get_site_entity()->getDisplayName()]),
	'name' => 'all',
	'value' => 1,
	'switch' => true,
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('newsletter:unsubscribe'),
]);
elgg_set_form_footer($footer);
