<?php
/**
 * Send a preview mail to the provided email address;
 *
 */

$guid = (int) get_input('guid');
$email = get_input('email');

if (empty($guid) || empty($email)) {
	register_error(elgg_echo('InvalidParameterException:MissingParameter'));
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	register_error(elgg_echo('InvalidParameterException:NoEntityFound'));
	forward(REFERER);
}
	
if (!elgg_instanceof($entity, 'object', Newsletter::SUBTYPE)) {
	register_error(elgg_echo('ClassException:ClassnameNotClass', array($guid, elgg_echo('item:object:' . Newsletter::SUBTYPE))));
	forward(REFERER);
}

newsletter_send_preview($entity, $email);

system_message(elgg_echo('newsletter:action:preview_mail:success'));
forward(REFERER);