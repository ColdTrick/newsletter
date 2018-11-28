<?php
/**
 * Action file to directly send a newsletter to its recipients
 */

$guid = (int) get_input('guid');
$status_notification = get_input('status_notification');
$show_in_archive = (int) get_input('show_in_archive');

if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$entity->scheduled = mktime(date('G'), 0, 1, date('n'), date('j'), date('Y'));
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

return elgg_ok_response('', elgg_echo('newsletter:action:send:success'), elgg_generate_entity_url($entity, 'log'));
