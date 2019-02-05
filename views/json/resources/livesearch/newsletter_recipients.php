<?php
/**
 * Procedure to find recipients for the newsletter
 */

// only loggedin users can access this page
elgg_gatekeeper();

$query = get_input('term');
$limit = (int) get_input('limit', 5);
$guid = (int) get_input('guid');

$result = [];

$return = function() use (&$result) {
	echo elgg_view_page('', json_encode(array_values($result)));
};

if (empty($query) || empty($guid)) {
	echo $return();
	return;
}

$entity = get_entity($guid);
// do we have a newsletter
if (!$entity instanceof Newsletter || !$entity->canEdit()) {
	echo $return();
	return;
}

// search for individual users
$users = elgg_search([
	'query' => $query,
	'type' => 'user',
	'search_type' => 'entities',
	'limit' => $limit,
]);

if (!empty($users)) {
	foreach ($users as $user) {
		$key = strtolower($user->name) . $user->guid;
		
		$result[$key] = json_decode(elgg_view('search/entity', [
			'entity' => $user,
			'input_name' => 'user_guids',
		]));
	}
}

if (!$entity->getContainerEntity() instanceof ElggGroup) {
	// search for groups
	$groups = elgg_search([
		'query' => $query,
		'type' => 'group',
		'search_type' => 'entities',
		'limit' => $limit,
	]);
	
	if (!empty($groups)) {
		foreach ($groups as $group) {
			$key = strtolower($group->name) . $group->guid;
			
			$result[$key] = json_decode(elgg_view('search/entity', [
				'entity' => $group,
				'input_name' => 'group_guids',
			]));
		}
	}
}

// email input
if (newsletter_is_email_address($query)) {
	if ($users = get_user_by_email($query)) {
		// found a user with this email address
		$key = strtolower($users[0]->name) . $users[0]->guid;
		
		$result[$key] = json_decode(elgg_view('search/entity', [
			'entity' => $users[0],
			'input_name' => 'user_guids',
		]));
	} else {
		// no user found
		$result[$query] = newsletter_format_email_recipient($query);
	}
}

ksort($result);
echo $return();
