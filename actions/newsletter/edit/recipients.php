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

elgg_make_sticky_form('newsletter/edit/recipients');

$guid = (int) get_input('guid');

$user_guids = get_input('user_guids');
$group_guids = get_input('group_guids');
$emails = get_input('emails');

$subscribers = (int) get_input('subscribers');
$members = (int) get_input('members');

if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof Newsletter || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$forward_url = REFERER;

$recipients = $entity->getRecipients();
if (empty($recipients)) {
	$forward_url = elgg_generate_entity_url($entity, 'edit', 'schedule');
}

// make sere we have the correct format
if (empty($user_guids)) {
	$user_guids = [];
} elseif (!is_array($user_guids)) {
	$user_guids = [$user_guids];
}
// filter duplicates
$user_guids = array_unique($user_guids);

if (empty($group_guids)) {
	$group_guids = [];
} elseif (!is_array($group_guids)) {
	$group_guids = [$group_guids];
}
// filter duplicates
$group_guids = array_unique($group_guids);

if (empty($emails)) {
	$emails = [];
} elseif (!is_array($emails)) {
	$emails = [$emails];
}
// filter duplicates
$emails = array_unique($emails);

// prepare save
$tmp = [
	'user_guids' => $user_guids,
	'group_guids' => $group_guids,
	'emails' => $emails,
	'subscribers' => $subscribers,
	'members' => $members,
];

// check for an uploaded CSV
if (elgg_get_uploaded_file('csv')) {
	$tmp = newsletter_process_csv_upload($tmp);
}

// save results
$entity->setRecipients($tmp);

elgg_clear_sticky_form('newsletter/edit/recipients');

return elgg_ok_response('', elgg_echo('newsletter:action:recipients:success'), $forward_url);
