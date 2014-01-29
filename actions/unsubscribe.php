<?php
/**
 * Unsubscribe the provided recipient from the newsletter(s)
 *
 */

$guid = (int) get_input("guid");
$recipient = get_input("recipient");
$all = (int) get_input("all");

$code = get_input("code");
$entity_guid = (int) get_input("entity_guid");

$forward_url = REFERER;

if (!empty($entity_guid) && !empty($recipient)) {
	$entity = get_entity($entity_guid);
	
	if (!empty($entity) && (empty($code) || newsletter_validate_unsubscribe_code($entity, $recipient, $code))) {
		$recipient_error = false;
		
		// what to unsubscribe
		if (!empty($guid)) {
			// unsubscribe one newsletter
			if (is_numeric($recipient) && ($user = get_user($recipient))) {
				if (newsletter_unsubscribe_user($user, $entity)) {
					$forward_url = "";
					system_message(elgg_echo("newsletter:action:unsubscribe:success:entity", array($entity->name)));
				} else {
					register_error(elgg_echo("newsletter:action:unsubscribe:error:entity", array($entity->name)));
				}
			} elseif (newsletter_is_email_address($recipient)) {
				if (newsletter_unsubscribe_email($recipient, $entity)) {
					$forward_url = "";
					system_message(elgg_echo("newsletter:action:unsubscribe:success:entity", array($entity->name)));
				} else {
					register_error(elgg_echo("newsletter:action:unsubscribe:error:entity", array($entity->name)));
				}
			} else {
				$recipient_error = true;
				register_error(elgg_echo("newsletter:action:unsubscribe:error:recipient", array($recipient)));
			}
		}
		
		// unsubscribe from all
		if (!empty($all)) {
			if (is_numeric($recipient) && ($user = get_user($recipient))) {
				if (newsletter_unsubscribe_all_user($user)) {
					$forward_url = "";
					system_message(elgg_echo("newsletter:action:unsubscribe:success:all"));
				} else {
					register_error(elgg_echo("newsletter:action:unsubscribe:error:all"));
				}
			} elseif (newsletter_is_email_address($recipient)) {
				if (newsletter_unsubscribe_all_email($recipient)) {
					$forward_url = "";
					system_message(elgg_echo("newsletter:action:unsubscribe:success:all"));
				} else {
					register_error(elgg_echo("newsletter:action:unsubscribe:error:all"));
				}
			} elseif (!$recipient_error) {
				register_error(elgg_echo("newsletter:action:unsubscribe:error:recipient", array($recipient)));
			}
		}
	} else {
		register_error(elgg_echo("newsletter:unsubscribe:error:code"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward($forward_url);
