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
elgg_require_js('forms/newsletter/edit/recipients');

echo elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:recipients:description'),
]);

echo elgg_view_field([
	'#type' => 'file',
	'#label' => elgg_echo('newsletter:recipients:csv'),
	'#help' => elgg_echo('newsletter:recipients:csv:description'),
	'name' => 'csv',
]);

// add subscribers
$subscriber_count = newsletter_get_subscribers($container, true);
echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:recipients:subscribers') . elgg_format_element('span', ['class' => 'mls'], "({$subscriber_count})"),
	'#help' => elgg_echo('newsletter:recipients:subscribers:help'),
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
	'#help' => elgg_echo('newsletter:recipients:members:help'),
	'name' => 'members',
	'value' => 1,
	'checked' => !empty(elgg_extract('members', $vars)),
	'switch' => true,
]);

// search for recipients
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('newsletter:recipients:recipient'),
	'#help' => elgg_echo('newsletter:recipients:recipient:description'),
	'name' => 'q',
	'id' => 'newsletter-recipients-autocomplete',
	'class' => 'elgg-input-autocomplete',
]);

// add specific users
$users_content = [];
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
		
		$users_content[] = elgg_view('input/autocomplete/item', [
			'entity' => $user,
			'input_name' => 'user_guids',
		]);
	}
	
	if (!empty($counter)) {
		$user_counter = "({$counter})";
	}
}

$users_content = elgg_format_element('ul', ['class' => 'elgg-list'], implode(PHP_EOL, $users_content));

$users_title = elgg_echo('item:user:user') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $user_counter);
echo elgg_view_module('info', $users_title, $users_content, ['class' => $class, 'id' => 'newsletter-recipients-users']);

// add specific groups
$groups_content = [];
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
		
		$groups_content[] = elgg_view('input/autocomplete/item', [
			'entity' => $group,
			'input_name' => 'group_guids',
		]);
	}
	
	if (!empty($counter)) {
		$group_counter = "({$counter})";
	}
}

$groups_content = elgg_format_element('ul', ['class' => 'elgg-list'], implode(PHP_EOL, $groups_content));

$groups_title = elgg_echo('item:group:group') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $group_counter);
echo elgg_view_module('info', $groups_title, $groups_content, ['class' => $class, 'id' => 'newsletter-recipients-groups']);

// add specific emails
$email_content = [];
$email_counter = '';
$class = 'hidden';

$emails = elgg_extract('emails', $vars);
if (!empty($emails)) {
	$counter = 0;
	
	foreach ($emails as $email) {
		$email_listing = newsletter_format_email_recipient($email);
		if (empty($email_listing)) {
			continue;
		}
		
		$class = '';
		$counter++;
		
		$email_content[] = elgg_extract('html', $email_listing);
	}
	
	if (!empty($counter)) {
		$email_counter = "({$counter})";
	}
}

$email_content = elgg_format_element('ul', ['class' => 'elgg-list'], implode(PHP_EOL, $email_content));

$emails_title = elgg_echo('newsletter:recipients:email') . elgg_format_element('span', ['class' => 'newsletter-counter mls'], $email_counter);
echo elgg_view_module('info', $emails_title, $email_content, ['class' => $class, 'id' => 'newsletter-recipients-emails']);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
