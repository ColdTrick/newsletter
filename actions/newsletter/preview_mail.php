<?php
/**
 * Send a preview mail to the provided email address;
 *
 */

$guid = (int) get_input('guid');
$email = get_input('email');

if (empty($guid) || empty($email)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

newsletter_send_preview($entity, $email);

system_message(elgg_echo('newsletter:action:preview_mail:success'));
forward(REFERER);