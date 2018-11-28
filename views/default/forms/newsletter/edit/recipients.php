<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Newsletter) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

$container = $entity->getContainerEntity();
elgg_require_js('newsletter/recipients');

echo elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:recipients:description'),
]);

echo elgg_view_field([
	'#type' => 'file',
	'#label' => elgg_echo('newsletter:recipients:csv'),
	'#help' => elgg_echo('newsletter:recipients:csv:description'),
	'name' => 'csv',
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('newsletter:recipients:recipient'),
	'#help' => elgg_echo('newsletter:recipients:recipient:description'),
	'name' => 'q',
	'class' => 'elgg-input-autocomplete',
]);

// recipient wrapper
// add subscribers

$subscriber_count = newsletter_get_subscribers($container, true);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:recipients:subscribers') . elgg_format_element('span', ['class' => 'mls'], "({$subscriber_count})"),
	'name' => 'subscribers',
	'value' => 1,
	'checked' => !empty(elgg_extract('subscribers', $vars)),
	'switch' => true,
]);

// add members
$member_count = 0;
if (!$container instanceof ElggGroup) {
	$member_count = elgg_get_entities([
		'type' => 'user',
		'count' => true,
	]);
	
	$member_count .= ' ' . elgg_echo('newsletter:recipients:members:site');
} else {
	$member_count = $container->getMembers([
		'count' => true,
	]);
	
	$member_count .= ' ' . elgg_echo('newsletter:recipients:members:group');
}

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:recipients:members') . elgg_format_element('span', ['class' => 'mls'], "({$member_count})"),
	'name' => 'members',
	'value' => 1,
	'checked' => !empty(elgg_extract('members', $vars)),
	'switch' => true,
]);

// add specific users
$users_content = '';
$user_counter = '';
$class = 'hidden';

$user_guids = elgg_extract('user_guids', $vars);
if (!empty($user_guids)) {
	$counter = 0;
	
	$users = elgg_get_entities([
		'type' => 'user',
		'guids' => $user_guids,
		'limit' => false,
		'batch' => true,
		'order_by_metadata' => [
			'name' => 'name',
		],
	]);
	
	/* @var $user ElggUser */
	foreach ($users as $user) {
		$class = '';
		$counter++;
		
		$user_listing = newsletter_format_recipient($user);
		
		$users_content .= elgg_extract('content', $user_listing);
	}
	
	if (!empty($counter)) {
		$user_counter = "({$counter})";
	}
}

$users_title = elgg_echo('item:user:user') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $user_counter);
$users = elgg_view_module('newsletter-recipients', $users_title, $users_content, ['class' => $class, 'id' => 'newsletter-recipients-users']);

// add specific groups
$groups_content = '';
$group_counter = '';
$class = 'hidden';

$group_guids = elgg_extract('group_guids', $vars);
if (!empty($group_guids)) {
	$counter = 0;
	
	$groups = elgg_get_entities([
		'type' => 'group',
		'guids' => $group_guids,
		'limit' => false,
		'batch' => true,
		'order_by_metadata' => [
			'name' => 'name',
		],
	]);
	
	/* @var $group ElggGroup */
	foreach ($groups as $group) {
		$class = '';
		$counter++;
		
		$group_listing = newsletter_format_recipient($group);

		$groups_content .= elgg_extract('content', $group_listing);
	}
	
	if (!empty($counter)) {
		$group_counter = "({$counter})";
	}
}

$groups_title = elgg_echo('item:group:group') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $group_counter);
$groups = elgg_view_module('newsletter-recipients', $groups_title, $groups_content, ['class' => $class, 'id' => 'newsletter-recipients-groups']);

// add specific emails
$email_content = '';
$email_counter = '';
$class = 'hidden';

$emails = elgg_extract('emails', $vars);
if (!empty($emails)) {
	$counter = 0;
	
	foreach ($emails as $email) {
		$email_listing = newsletter_format_recipient($email);

		if (empty($email_listing)) {
			continue;
		}
		
		$class = '';
		$counter++;
		
		$email_content .= elgg_extract('content', $email_listing);
	}
	
	if (!empty($counter)) {
		$email_counter = "({$counter})";
	}
}

$emails_title = elgg_echo('newsletter:recipients:email') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $email_counter);
$emails = elgg_view_module('newsletter-recipients', $emails_title, $email_content, ['class' => $class, 'id' => 'newsletter-recipients-emails']);

$icon_options = [
	'class' => ['elgg-icon-info', 'mlm'],
	'title' => elgg_echo('newsletter:recipients:tooltip'),
];

$wrapper_title = elgg_echo('newsletter:recipients') . elgg_view('output/icon', $icon_options);

echo elgg_view_module('newsletter-recipients-wrapper', $wrapper_title, $checkboxes . $users . $groups . $emails, ['id' => 'newsletter-recipients-wrapper']);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
