<?php

$entity = elgg_extract('entity', $vars);

if (!empty($entity)) {
	$title = elgg_get_sticky_value('newsletter_edit', 'title', $entity->title);
	$subject = elgg_get_sticky_value('newsletter_edit', 'subject', $entity->subject);
	$from = elgg_get_sticky_value('newsletter_edit', 'from', $entity->from);
	$description = elgg_get_sticky_value('newsletter_edit', 'description', $entity->description);
	$access_id = (int) elgg_get_sticky_value('newsletter_edit', 'access_id', $entity->access_id);
	$tags = elgg_get_sticky_value('newsletter_edit', 'tags', $entity->tags);
	
	echo elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);
	
	$container_guid = $entity->container_guid;
} else {
	$title = elgg_get_sticky_value('newsletter_edit', 'title');
	$subject = elgg_get_sticky_value('newsletter_edit', 'subject');
	$from = elgg_get_sticky_value('newsletter_edit', 'from');
	$description = elgg_get_sticky_value('newsletter_edit', 'description');
	$access_id = (int) elgg_get_sticky_value('newsletter_edit', 'access_id', get_default_access());
	$tags = elgg_get_sticky_value('newsletter_edit', 'tags');
	
	$container_guid = (int) elgg_extract('container_guid', $vars);
}

elgg_clear_sticky_form('newsletter_edit');

$field = elgg_format_element('label', ['for' => 'newsletter-title'], elgg_echo('title'));
$field .= elgg_view('input/text', [
	'name' => 'title',
	'value' => $title,
	'id' => 'newsletter-title',
]);
echo elgg_format_element('div', [], $field);

$field = elgg_format_element('label', ['for' => 'newsletter-subject'], elgg_echo('newsletter:edit:subject'));
$field .= elgg_view('input/text', [
	'name' => 'subject',
	'value' => $subject,
	'id' => 'newsletter-subject',
]);
echo elgg_format_element('div', [], $field);

if (newsletter_custom_from_enabled()) {
	$field = elgg_format_element('label', ['for' => 'newsletter-from'], elgg_echo('newsletter:edit:from'));
	$field .= elgg_view('input/email', ['name' => 'from', 'value' => $from, 'id' => 'newsletter-from']);
	$field .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:edit:from:description', [elgg_format_element('strong', [], elgg_get_site_entity()->email)]));
	echo elgg_format_element('div', [], $field);
}

$field = elgg_format_element('label', ['for' => 'newsletter-description'], elgg_echo('description'));
$field .= elgg_view('input/text', [
	'name' => 'description',
	'value' => $description,
	'id' => 'newsletter-description',
]);
$field .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('newsletter:edit:description:description'));
echo elgg_format_element('div', [], $field);

$field = elgg_format_element('label', ['for' => 'newsletter-tags'], elgg_echo('tags'));
$field .= elgg_view('input/tags', [
	'name' => 'tags',
	'value' => $tags,
	'id' => 'newsletter-tags',
]);
echo elgg_format_element('div', [], $field);

$field = elgg_format_element('label', ['for' => 'newsletter-access-id'], elgg_echo('access'));
$field .= elgg_view('input/access', [
	'name' => 'access_id',
	'value' => $access_id,
	'id' => 'newsletter-access-id',
	'class' => 'mls',
]);
echo elgg_format_element('div', [], $field);

$foot = elgg_view('input/hidden', ['name' => 'container_guid', 'value' => $container_guid]);
$foot .= elgg_view('input/submit', ['value' => elgg_echo('save')]);
echo elgg_format_element('div', ['class' => 'elgg-foot'], $foot);
