<?php
/**
 * Action file to save the schedule for a newsletter
 *
 */

elgg_make_sticky_form('newsletter_schedule');

$guid = (int) get_input('guid');
$date = (int) get_input('date');
$hour = (int) get_input('hour');
$status_notification = get_input('status_notification');
$show_in_archive = (int) get_input('show_in_archive');

if (empty($guid) || empty($date)) {
	register_error(elgg_echo('InvalidParameterException:MissingParameter'));
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	register_error(elgg_echo('InvalidParameterException:NoEntityFound'));
	forward(REFERER);
}

if (!elgg_instanceof($entity, 'object', Newsletter::SUBTYPE)) {
	register_error(elgg_echo('ClassException:ClassnameNotClass', [$guid, elgg_echo('item:object:' . Newsletter::SUBTYPE)]));
	forward(REFERER);
}

$forward_url = REFERER;

// convert date from GMT to local
// and add hours
$date = mktime($hour, 0, 0, date('n', $date), date('j', $date), date('Y', $date));

$entity->scheduled = $date;
$entity->status = 'scheduled';
$entity->show_in_archive = $show_in_archive;

// status notification email address
if (!empty($status_notification)) {
	$entity->status_notification = $status_notification;
} else {
	unset($entity->status_notification);
}

// some cleanup
system_message(elgg_echo('newsletter:action:schedule:success'));

elgg_clear_sticky_form('newsletter_schedule');
if (elgg_instanceof($entity->getContainerEntity(), 'group')) {
	$forward_url = 'newsletter/group/' . $entity->getContainerGUID();
} else {
	$forward_url = 'newsletter/site';
}

forward($forward_url);
