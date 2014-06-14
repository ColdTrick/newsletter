<?php

/**
 * Procedure to find recipients for the newsletter
 *
 * Should return a JSON result
 */

$result = array();

// only loggedin users can access this page
if (!elgg_is_logged_in()) {
	header("Content-Type: application/json");
	echo json_encode($result);
	exit();
}

$query = get_input("term");
$limit = (int) get_input("limit", 5);
$guid = (int) get_input("guid");

if (!empty($query) && !empty($guid)) {
	$entity = get_entity($guid);
	// do we have a newsletter
	if (!empty($entity) && elgg_instanceof($entity, "object", Newsletter::SUBTYPE) && $entity->canEdit()) {
		$site = elgg_get_site_entity();
		$dbprefix = elgg_get_config("dbprefix");
		$filtered_query = sanitise_string($query);
		
		// search for individual users
		$options = array(
			"type" => "user",
			"site_guids" => false,
			"limit" => $limit,
			"relationship" => "member_of_site",
			"relationship_guid" => $site->getGUID(),
			"inverse_relationship" => true,
			"joins" => array("JOIN " . $dbprefix . "users_entity ue ON e.guid = ue.guid"),
			"wheres" => array("(ue.name LIKE '%" . $filtered_query . "%' OR ue.email LIKE '%" . $filtered_query . "%' OR ue.username LIKE '%" . $filtered_query . "%')")
		);
		
		$users = elgg_get_entities_from_relationship($options);
		
		if (!empty($users)) {
			foreach ($users as $user) {
				
				$key = strtolower($user->name) . $user->getGUID();
				
				$result[$key] = newsletter_format_recipient($user);
			}
		}
		
		if (!elgg_instanceof($entity->getContainerEntity(), "group")) {
			// search for groups
			$options = array(
				"type" => "group",
				"limit" => $limit,
				"joins" => array("JOIN " . $dbprefix . "groups_entity ge ON e.guid = ge.guid"),
				"wheres" => array("(ge.name LIKE '%" . $filtered_query . "%' OR ge.description LIKE '%" . $filtered_query . "%')")
			);
			
			$groups = elgg_get_entities($options);
			
			if (!empty($groups)) {
				foreach ($groups as $group) {
					$key = strtolower($group->name) . $group->getGUID();
					
					$result[$key] = newsletter_format_recipient($group);
				}
			}
		}
		
		// email input
		if (newsletter_is_email_address($query)) {
			if ($users = get_user_by_email($query)) {
				// found a user with this email address
				$key = strtolower($users[0]->name) . $users[0]->getGUID();
				
				$result[$key] = newsletter_format_recipient($users[0]);
			} else {
				// no user found
				$result[$query] = newsletter_format_recipient($query);
			}
		}
	}
}

ksort($result);

header("Content-Type: application/json");
echo json_encode(array_values($result));
