<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggObject) {
	return;
}

// data for embedding
$data = [
	'data-title' => $entity->getDisplayName(),
	'data-description' => $entity->description,
	'data-url' => $entity->getURL(),
];

// excerpt support
$excerpt = $entity->excerpt;
if (empty($excerpt)) {
	$excerpt = elgg_get_excerpt($entity->description);
}
if (!empty($excerpt)) {
	$data['data-excerpt'] = $excerpt;
}

// icon support
$icon_size = elgg_extract('icon_size', $vars, 'medium');
unset($vars['icon_size']);
if ($entity->hasIcon($icon_size)) {
	$data['data-icon-url'] = $entity->getIconURL([
		'size' => $icon_size,
		'use_cookie' => false,
	]);
}

// build listing view
$params = [
	'entity' => $entity,
	'title' => $entity->getDisplayName(),
	'access' => false,
	'tags' => false,
	'metadata' => false,
	'content' => $excerpt,
];
$params = $params + $vars;

echo elgg_format_element('div', $data, elgg_view('object/elements/summary', $params));
