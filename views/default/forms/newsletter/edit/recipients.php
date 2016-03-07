<?php

$entity = elgg_extract('entity', $vars);
$container = $entity->getContainerEntity();

$recipients = $entity->getRecipients();
if (!empty($recipients)) {
	
	$user_guids = elgg_get_sticky_value('newsletter_recipients', 'user_guids', elgg_extract('user_guids', $recipients));
	$group_guids = elgg_get_sticky_value('newsletter_recipients', 'group_guids', elgg_extract('group_guids', $recipients));
	$emails = elgg_get_sticky_value('newsletter_recipients', 'emails', elgg_extract('emails', $recipients));
	
	$subscribers = (int) elgg_get_sticky_value('newsletter_recipients', 'subscribers', elgg_extract('subscribers', $recipients));
	$members = (int) elgg_get_sticky_value('newsletter_recipients', 'members', elgg_extract('members', $recipients));
} else {
	$user_guids = elgg_get_sticky_value('newsletter_recipients', 'user_guids');
	$group_guids = elgg_get_sticky_value('newsletter_recipients', 'group_guids');
	$emails = elgg_get_sticky_value('newsletter_recipients', 'emails');
	
	$subscribers = (int) elgg_get_sticky_value('newsletter_recipients', 'subscribers');
	$members = (int) elgg_get_sticky_value('newsletter_recipients', 'members');
}

elgg_require_js('newsletter/recipients');

echo elgg_format_element('div', [], elgg_view('output/longtext', ['value' => elgg_echo('newsletter:recipients:description')]));

$file = elgg_format_element('label', ['for' => 'newsletter-recipients-csv'], elgg_echo('newsletter:recipients:csv'));
$file .= elgg_view('input/file', ['name' => 'csv', 'id' => 'newsletter-recipients-csv']);
$file .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:recipients:csv:description'));
echo elgg_format_element('div', [], $file);

$autocomplete = elgg_format_element('label', ['for' => 'newsletter-recipients-autocomplete'], elgg_echo('newsletter:recipients:recipient'));
$autocomplete .= elgg_view('input/text', ['name' => 'q', 'id' => 'newsletter-recipients-autocomplete', 'class' => 'elgg-input-autocomplete']);
$autocomplete .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:recipients:recipient:description'));
echo elgg_format_element('div', [], $autocomplete);

// recipient wrapper
// add subscribers

$subscriber_count = newsletter_get_subscribers($container, true);

$checkbox_subscribers = elgg_view('input/checkbox', [
	'name' => 'subscribers',
	'value' => '1',
	'id' => 'newsletter-recipients-subscribers',
	'checked' => !empty($subscribers),
]);
$checkbox_subscribers_label = elgg_echo('newsletter:recipients:subscribers') . elgg_format_element('span', ['class' => 'mls'], "({$subscriber_count})");
$checkbox_subscribers .= elgg_format_element('label', ['for' => 'newsletter-recipients-subscribers'], $checkbox_subscribers_label);

$checkboxes = elgg_format_element('div', [], $checkbox_subscribers);

// add members
$member_count = 0;
if (elgg_instanceof($container, 'site')) {
	$member_count = $container->getEntities([
		'site_guids' => false,
		'count' => true,
		'type' => 'user',
	]);
	
	$member_count .= ' ' . elgg_echo('newsletter:recipients:members:site');
} elseif (elgg_instanceof($container, 'group')) {
	$member_count = $container->getMembers(0, 0, true);
	
	$member_count .= ' ' . elgg_echo('newsletter:recipients:members:group');
}

$checkbox_members = elgg_view('input/checkbox', [
	'name' => 'members',
	'value' => '1',
	'id' => 'newsletter-recipients-members',
	'checked' => !empty($members),
]);
$checkbox_members_label = elgg_echo('newsletter:recipients:members') . elgg_format_element('span', ['class' => 'mls'], "({$member_count})");
$checkbox_members .= elgg_format_element('label', ['for' => 'newsletter-recipients-members'], $checkbox_members_label);

$checkboxes .= elgg_format_element('div', [], $checkbox_members);

// add specific users
$users_content = '';
$class = 'hidden';
$counter = '';

if (!empty($user_guids)) {
	$counter = 0;
	
	foreach ($user_guids as $user_guid) {
		$user = get_user($user_guid);
		
		if (!empty($user)) {
			$class = '';
			$counter++;
			
			$user_listing = newsletter_format_recipient($user);
			
			$users_content .= elgg_extract('content', $user_listing);
		}
	}
	
	if (!empty($counter)) {
		$counter = "({$counter})";
	} else {
		$counter = '';
	}
}

$users_title = elgg_echo('item:user') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $counter);
$users = elgg_view_module('newsletter-recipients', $users_title, $users_content, ['class' => $class, 'id' => 'newsletter-recipients-users']);

// add specific groups
$groups_content = '';
$class = 'hidden';
if (!empty($group_guids)) {
	$counter = 0;
	
	foreach ($group_guids as $group_guid) {
		$group = get_entity($group_guid);
			
		if (!empty($group) && elgg_instanceof($group, 'group')) {
			$class = '';
			$counter++;
			
			$group_listing = newsletter_format_recipient($group);

			$groups_content .= elgg_extract('content', $group_listing);
		}
	}
	
	if (!empty($counter)) {
		$counter = "({$counter})";
	} else {
		$counter = '';
	}
}

$groups_title = elgg_echo('groups') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $counter);
$groups = elgg_view_module('newsletter-recipients', $groups_title, $groups_content, ['class' => $class, 'id' => 'newsletter-recipients-groups']);

// add specific emails
$email_content = '';
$class = 'hidden';
if (!empty($emails)) {
	$counter = 0;
	
	foreach ($emails as $email) {
		$email_listing = newsletter_format_recipient($email);

		if (!empty($email_listing)) {
			$class = '';
			$counter++;
			
			$email_content .= elgg_extract('content', $email_listing);
		}
	}
	
	if (!empty($counter)) {
		$counter = "({$counter})";
	} else {
		$counter = '';
	}
}

$emails_title = elgg_echo('newsletter:recipients:email') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $counter);
$emails = elgg_view_module('newsletter-recipients', $emails_title, $email_content, ['class' => $class, 'id' => 'newsletter-recipients-emails']);

$icon_options = [
	'class' => ['elgg-icon-info', 'mlm'],
	'title' => elgg_echo('newsletter:recipients:tooltip'),
];

$wrapper_title = elgg_echo('newsletter:recipients') . elgg_view('output/icon', $icon_options);

echo elgg_view_module('newsletter-recipients-wrapper', $wrapper_title, $checkboxes . $users . $groups . $emails, ['id' => 'newsletter-recipients-wrapper']);

$foot = elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);
$foot .= elgg_view('input/submit', ['value' => elgg_echo('save')]);
echo elgg_format_element('div', ['class' => 'elgg-foot'], $foot);
