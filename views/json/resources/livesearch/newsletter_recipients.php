<?php
/**
 * Livesearch endpoint to find recipients for the newsletter
 *
 * @uses $vars['limit']          (int)    number of results to return
 * @uses $vars['term']           (string) search term (for username and displayname)
 * @uses $vars['name']           (string) the input name to be used when submitting the selected value
 * @uses $vars['guid']           (int)    the GUID of the newsletter te add recipients for
 * @uses $vars['include_banned'] (bool)   include banned users in search results
 */

elgg_gatekeeper();

$limit = (int) elgg_extract('limit', $vars, elgg_get_config('default_limit'));
$query = elgg_extract('term', $vars, elgg_extract('q', $vars));
$guid = (int) elgg_extract('guid', $vars);
$include_banned = (bool) elgg_extract('include_banned', $vars, false);

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
$options = [
	'query' => $query,
	'type' => 'user',
	'search_type' => 'entities',
	'limit' => $limit,
	'metadata_name_value_pairs' => [],
];
if (!$include_banned) {
	$options['metadata_name_value_pairs'][] = [
		'name' => 'banned',
		'value' => 'no',
	];
}

$users = elgg_search($options);

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
		/* @var $user \ElggUser */
		$user = $users[0];
		if ($include_banned || !$user->isBanned()) {
			// found a user with this email address
			$key = strtolower($user->getDisplayName()) . $user->guid;
			
			$result[$key] = json_decode(elgg_view('search/entity', [
				'entity' => $user,
				'input_name' => 'user_guids',
			]));
		}
	} else {
		// no user found
		$result[$query] = newsletter_format_email_recipient($query);
	}
}

ksort($result);
echo $return();
