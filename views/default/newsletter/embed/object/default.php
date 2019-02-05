<?php

$entity = elgg_extract('entity', $vars);
if (!elgg_instanceof($entity, 'object')) {
	return;
}

$container = $entity->getContainerEntity();

// data for embedding
$data = [
	'data-title' => $entity->title,
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
if ($entity->icontime) {
	$data['data-icon-url'] = $entity->getIconURL('medium');
}

// build listing view
echo elgg_format_element('div', $data, elgg_view('object/elements/summary', [
	'entity' => $entity,
	'title' => $entity->title,
	'access' => false,
	'tags' => false,
	'metadata' => false,
	'content' => $excerpt,
]));
