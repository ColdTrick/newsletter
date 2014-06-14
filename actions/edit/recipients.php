<?php
/**
 * Add recipients to a newsletter.
 *
 * This can be done by providing
 * - user_guids
 * - group_guids
 * - email addresses
 * - set subscibers
 * - set members (site or group)
 * - uploading a CSV with email addresses
 */

elgg_make_sticky_form("newsletter_recipients");

$guid = (int) get_input("guid");

$user_guids = get_input("user_guids");
$group_guids = get_input("group_guids");
$emails = get_input("emails");

$subscribers = (int) get_input("subscribers");
$members = (int) get_input("members");

$forward_url = REFERER;

if (!empty($guid)) {
	$entity = get_entity($guid);

	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			
			$recipients = $entity->getRecipients();
			if (empty($recipients)) {
				$forward_url = "newsletter/edit/" . $entity->getGUID() . "/schedule";
			}
			
			// make sere we have the correct format
			if (empty($user_guids)) {
				$user_guids = array();
			} elseif (!is_array($user_guids)) {
				$user_guids = array($user_guids);
			}
			// filter duplicates
			$user_guids = array_unique($user_guids);
			
			if (empty($group_guids)) {
				$group_guids = array();
			} elseif (!is_array($group_guids)) {
				$group_guids = array($group_guids);
			}
			// filter duplicates
			$group_guids = array_unique($group_guids);
			
			if (empty($emails)) {
				$emails = array();
			} elseif (!is_array($emails)) {
				$emails = array($emails);
			}
			// filter duplicates
			$emails = array_unique($emails);
			
			// prepare save
			$tmp = array(
				"user_guids" => $user_guids,
				"group_guids" => $group_guids,
				"emails" => $emails,
				"subscribers" => $subscribers,
				"members" => $members
			);
			
			// check for an uploaded CSV
			if (get_uploaded_file("csv")) {
				$tmp = newsletter_process_csv_upload($tmp);
			}
			
			// save results
			$entity->setRecipients($tmp);
			
			system_message(elgg_echo("newsletter:action:recipients:success"));
			
			elgg_clear_sticky_form("newsletter_recipients");

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
