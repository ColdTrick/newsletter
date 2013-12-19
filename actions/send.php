<?php
/**
 * Action file to directly send a newsletter to its recipients
 */

$guid = (int) get_input("guid");
$status_notification = get_input("status_notification");
$show_in_archive = (int) get_input("show_in_archive");

$forward_url = REFERER;

if (!empty($guid)) {
	$entity = get_entity($guid);
	
	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			$entity->scheduled = mktime(date("G"), 0, 1, date("n"), date("j"), date("Y"));
			$entity->status = "sending";
			$entity->show_in_archive = $show_in_archive;
			
			// status notification email address
			if (!empty($status_notification)) {
				$entity->status_notification = $status_notification;
			} else {
				unset($entity->status_notification);
			}
			
			newsletter_start_commandline_sending($entity);
			
			$forward_url = "newsletter/log/" . $entity->getGUID();
			
			system_message(elgg_echo("newsletter:action:send:success"));
		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward($forward_url);
