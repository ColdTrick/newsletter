<?php
/**
 * Procedure to find recipients for the newsletter
 *
 * Should return a JSON result
 *
 * @todo rework!!!!!
 */

$result = [];

header('Content-Type: application/json');

// only loggedin users can access this page
if (!elgg_is_logged_in()) {
	echo json_encode($result);
	exit();
}

$query = get_input('term');
$limit = (int) get_input('limit', 5);
$guid = (int) get_input('guid');

if (empty($query) || empty($guid)) {
	echo json_encode($result);
	exit();
}

$entity = get_entity($guid);
// do we have a newsletter
if (!$entity instanceof Newsletter || !$entity->canEdit()) {
	echo json_encode($result);
	exit();
}

// search for individual users
$users = elgg_search([
	'query' => $query,
	'type' => 'user',
	'limit' => $limit,
]);

if (!empty($users)) {
	foreach ($users as $user) {
		
		$key = strtolower($user->name) . $user->guid;
		
		$result[$key] = newsletter_format_recipient($user);
	}
}

if (!$entity->getContainerEntity() instanceof ElggGroup) {
	// search for groups
	$groups = elgg_search([
		'query' => $query,
		'type' => 'group',
		'limit' => $limit,
	]);
	
	if (!empty($groups)) {
		foreach ($groups as $group) {
			$key = strtolower($group->name) . $group->guid;
			
			$result[$key] = newsletter_format_recipient($group);
		}
	}
}

// email input
if (newsletter_is_email_address($query)) {
	if ($users = get_user_by_email($query)) {
		// found a user with this email address
		$key = strtolower($users[0]->name) . $users[0]->guid;
		
		$result[$key] = newsletter_format_recipient($users[0]);
	} else {
		// no user found
		$result[$query] = newsletter_format_recipient($query);
	}
}

ksort($result);
echo json_encode(array_values($result));
