<?php

$entity = elgg_extract('entity', $vars);
$full_view = (bool) elgg_extract('full_view', $vars, false);

if ($full_view) {
	// only handle listing view
	return;
}

$entity_menu = '';
if (!elgg_in_context('widgets')) {
	$entity_menu = elgg_view_menu('entity', [
		'entity' => $entity,
		'handler' => 'newsletter',
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	]);
}

// listing view
$icon = elgg_view_entity_icon($entity, 'small');

$excerpt = '';
if ($entity->description) {
	$excerpt = elgg_get_excerpt($entity->description);
}

$subtitle = '';
switch ($entity->status) {
	case 'scheduled':
		$subtitle = elgg_format_element('strong', [], elgg_echo('newsletter:entity:scheduled') . ': ');
		$subtitle .= date(elgg_echo('friendlytime:date_format'), $entity->scheduled);
		break;
	case 'sent':
		$subtitle = elgg_format_element('strong', [], elgg_echo('newsletter:entity:sent') . ': ');
		$subtitle .= elgg_view_friendly_time($entity->start_time);
		break;
}

$params = [
	'entity' => $entity,
	'metadata' => $entity_menu,
	'subtitle' => $subtitle,
	'content' => $excerpt,
];

$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($icon, $list_body);
