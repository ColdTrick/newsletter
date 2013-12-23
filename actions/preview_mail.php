<?php
/**
 * Send a preview mail to the provided email address;
 *
 */

$guid = (int) get_input("guid");
$email = get_input("email");

if (!empty($guid) && !empty($email)) {
	$entity = get_entity($guid);

	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			
			newsletter_send_preview($entity, $email);
			
			system_message(elgg_echo("newsletter:action:preview_mail:success"));
		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward(REFERER);