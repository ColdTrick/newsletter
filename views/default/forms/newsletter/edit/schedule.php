<?php

$entity = elgg_extract('entity', $vars);

$entity_date = gmmktime(0, 0, 0);
$entity_date += (24 * 60 * 60); // add one day for scheduling
$entity_hour = 0;
if ($entity->scheduled) {
	// input/date expects GMT timestamp
	$entity_date = gmmktime(0, 0, 0, date('n', $entity->scheduled), date('j', $entity->scheduled), date('Y', $entity->scheduled));
	$entity_hour = date('H', $entity->scheduled);
}

$date = elgg_get_sticky_value('newsletter_schedule', 'date', $entity_date);
$hour = (int) elgg_get_sticky_value('newsletter_schedule', 'hour', $entity_hour);
$status_notification = elgg_get_sticky_value('newsletter_schedule', 'status_notification', $entity->status_notification);
if (isset($entity->show_in_archive)) {
	$show_in_archive = (int) $entity->show_in_archive;
} else {
	$show_in_archive = 1;
}
$show_in_archive = (int) elgg_get_sticky_value('newsletter_schedule', 'show_in_archive', $show_in_archive);

elgg_clear_sticky_form('newsletter_schedule');

echo elgg_format_element('div', [], elgg_view('output/longtext', ['value' => elgg_echo('newsletter:schedule:description')]));

$date_input = elgg_format_element('label', ['for' => 'date'], elgg_echo("newsletter:schedule:date"));
$date_input .= elgg_view('input/date', [
	'name' => 'date',
	'value' => $date,
	'timestamp' => true,
	'class' => 'mhm',
]);

$date_input .= '@';

$date_input .= elgg_format_element('label', ['for' => 'newsletter-schedule-hour', 'class' => 'hidden'], elgg_echo('newsletter:schedule:time'));
$date_input .= elgg_view('input/select', [
	'name' => 'hour',
	'value' => $hour,
	'options' => range(0, 23),
	'class' => 'mlm',
	'id' => 'newsletter-schedule-hour',
]);
$date_input .= ':00';
echo elgg_format_element('div', [], $date_input);

$status_class = 'newsletter-status-notification';
if (empty($status_notification)) {
	$status_notification_input = elgg_view('input/checkbox', [
		'name' => 'status_notification_me',
		'value' => elgg_get_logged_in_user_entity()->email,
		'id' => 'newsletter-status-notification-me',
		'default' => false,
	]);
	$status_notification_input .= elgg_format_element('label', ['for' => 'newsletter-status-notification-me'], elgg_echo('newsletter:schedule:status_notification:me'));
	$status_notification_input .= elgg_view('output/url', [
		'text' => elgg_echo('newsletter:schedule:status_notification:toggle'),
		'href' => '#',
		'id' => 'newsletter-status-notification-toggle',
		'class' => 'mls',
	]);
	echo elgg_format_element('div', ['class' => $status_class], $status_notification_input);
	
	$status_class .= ' hidden';
}

$email_input = elgg_format_element('label', ['for' => 'newsletter-status-notification'], elgg_echo('newsletter:schedule:status_notification'));
$email_input .= elgg_view('input/email', [
	'name' => 'status_notification',
	'value' => $status_notification,
	'id' => 'newsletter-status-notification',
]);
$email_input .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:schedule:status_notification:description'));
echo elgg_format_element('div', ['class' => $status_class], $email_input);

$show_in_archive_input = elgg_view('input/checkbox', [
	'name' => 'show_in_archive',
	'value' => '1',
	'id' => 'newsletter-show-in-archive',
	'checked' => $show_in_archive,
]);
$show_in_archive_input .= elgg_format_element('label', ['for' => 'newsletter-show-in-archive'], elgg_echo("newsletter:schedule:show_in_archive"));
echo elgg_format_element('div', [], $show_in_archive_input);

$foot = elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);

$save_and_send_now_options = [
	'value' => elgg_echo('newsletter:schedule:send'),
	'class' => ['elgg-button-action', 'float-alt'],
	'id' => 'newsletter-send-now',
];
$save_and_schedule_options = [
	'value' => elgg_echo('newsletter:schedule:save'),
	'class' => ['elgg-button-submit'],
];

if (!$entity->getRecipients()) {
	$save_and_send_now_options['class'][] = 'elgg-requires-confirmation';
	$save_and_send_now_options['data-confirm'] = elgg_echo('newsletter:schedule:no_recipients');
	
	$save_and_schedule_options['class'][] = 'elgg-requires-confirmation';
	$save_and_schedule_options['data-confirm'] = elgg_echo('newsletter:schedule:no_recipients');
}

$foot .= elgg_view('input/button', $save_and_send_now_options);
$foot .= elgg_view('input/submit', $save_and_schedule_options);

echo elgg_format_element('div', ['class' => 'elgg-foot'], $foot);
