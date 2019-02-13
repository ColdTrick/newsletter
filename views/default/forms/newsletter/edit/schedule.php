<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Newsletter) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:schedule:description'),
]);

echo elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('newsletter:schedule:date'),
	'name' => 'date',
	'value' => elgg_extract('date', $vars, elgg_extract('scheduled', $vars)),
	'timestamp' => true,
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'time',
	'#label' => elgg_echo('newsletter:schedule:time'),
	'name' => 'hour',
	'value' => elgg_extract('hour', $vars, elgg_extract('scheduled', $vars)),
	'format' => 'G:i',
	'step' => (60 * 60), // hour steps
	'timestamp' => true,
]);

echo elgg_view_field([
	'#type' => 'email',
	'#label' => elgg_echo('newsletter:schedule:status_notification'),
	'#help' => elgg_echo('newsletter:schedule:status_notification:description'),
	'name' => 'status_notification',
	'value' => elgg_extract('status_notification', $vars),
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo("newsletter:schedule:show_in_archive"),
	'name' => 'show_in_archive',
	'value' => 1,
	'checked' => elgg_extract('show_in_archive', $vars),
	'switch' => true,
]);

// footer
$save_and_send_now_options = [
	'#type' => 'submit',
	'name' => 'send',
	'value' => elgg_echo('newsletter:schedule:send'),
];
$save_and_schedule_options = [
	'#type' => 'submit',
	'value' => elgg_echo('newsletter:schedule:save'),
];

if (!$entity->getRecipients()) {
	$save_and_send_now_options['class'][] = 'elgg-requires-confirmation';
	$save_and_send_now_options['data-confirm'] = elgg_echo('newsletter:schedule:no_recipients');
	
	$save_and_schedule_options['class'][] = 'elgg-requires-confirmation';
	$save_and_schedule_options['data-confirm'] = elgg_echo('newsletter:schedule:no_recipients');
}

$footer = elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		$save_and_schedule_options,
		$save_and_send_now_options,
	],
]);
elgg_set_form_footer($footer);
