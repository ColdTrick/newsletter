<?php
/**
 * Send a preview mail to the provided email address;
 *
 */

$guid = (int) get_input('guid');
$email = get_input('email');

if (empty($guid) || empty($email)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

newsletter_send_preview($entity, $email);

return elgg_ok_response('', elgg_echo('newsletter:action:preview_mail:success'));
