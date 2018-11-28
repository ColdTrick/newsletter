<?php
/**
 * Action file to save the schedule for a newsletter
 */

use Elgg\Values;

elgg_make_sticky_form('newsletter/edit/schedule');

$guid = (int) get_input('guid');
$date = (int) get_input('date');
$hour = (int) get_input('hour');
$status_notification = get_input('status_notification');
$show_in_archive = (int) get_input('show_in_archive');

$send = get_input('send'); // different submit button

if (empty($guid) || empty($date)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof Newsletter || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$date = Values::normalizeTime($date);
$date->modify('midnight');
$date->modify("+{$hour} seconds");

$entity->scheduled = $date->getTimestamp();
$entity->status = 'scheduled';
$entity->show_in_archive = $show_in_archive;

// status notification email address
if (!empty($status_notification)) {
	$entity->status_notification = $status_notification;
} else {
	unset($entity->status_notification);
}

elgg_clear_sticky_form('newsletter/edit/schedule');

$forward_url = elgg_generate_url('collection:object:newsletter:site');
$container = $entity->getContainerEntity();
if ($container instanceof ElggGroup) {
	$forward_url = elgg_generate_url('collection:object:newsletter:group', [
		'guid' => $container->guid,
	]);
}

if (!empty($send)) {
	
	// adjust scheduled time to current hour
	$date = Values::normalizeTime(time());
	$date->modify('-' . $date->format('s') . ' seconds');
	$date->modify('-' . $date->format('i') . ' minutes');
	$date->modify('+1 second');
	
	$entity->scheduled = $date->getTimestamp();
	
	// start sending
	newsletter_start_commandline_sending($entity);
	
	// need some time to start in order to view the log file
	sleep(2);
	
	// forward to log page
	$forward_url = elgg_generate_entity_url($entity, 'log');
}

return elgg_ok_response('', elgg_echo('newsletter:action:schedule:success'), $forward_url);
