<?php
/**
 * Action file to directly send a newsletter to its recipients
 */

$guid = (int) get_input("guid");
$status_notification = get_input("status_notification");
$show_in_archive = (int) get_input("show_in_archive");

if (empty($guid)) {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	forward(REFERER);
}

if (!elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
	register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
}

$entity->scheduled = mktime(date("G"), 0, 1, date("n"), date("j"), date("Y"));
$entity->show_in_archive = $show_in_archive;

// status notification email address
if (!empty($status_notification)) {
	$entity->status_notification = $status_notification;
} else {
	unset($entity->status_notification);
}

newsletter_start_commandline_sending($entity);

// sleep to make sure the log page has content
sleep(2);

system_message(elgg_echo("newsletter:action:send:success"));
forward("newsletter/log/" . $entity->getGUID());
