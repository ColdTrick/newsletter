<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Newsletter) {
	return;
}

if ((bool) elgg_extract('full_view', $vars, false)) {
	// only handle listing view
	return;
}

// listing view
$excerpt = '';
if ($entity->description) {
	$excerpt = elgg_get_excerpt($entity->description);
}

$imprint = [];
switch ($entity->status) {
	case 'scheduled':
		
		$content = elgg_echo('newsletter:entity:scheduled') . ': ';
		$content .= elgg_view('output/date', [
			'value' => $entity->scheduled,
			'format' => elgg_echo('friendlytime:date_format'),
		]);
		
		$imprint[] = [
			'icon_name' => 'clock',
			'content' => $content,
		];
		break;
	case 'sent':
		
		$content = elgg_echo('newsletter:entity:sent') . ': ';
		$content .= elgg_view_friendly_time($entity->start_time);
		
		$imprint[] = [
			'icon_name' => 'check',
			'content' => $content,
		];
		break;
}

$params = [
	'icon' => false,
	'entity' => $entity,
	'content' => $excerpt,
	'byline' => false,
	'time' => false,
	'imprint' => $imprint,
];

$params = $params + $vars;
echo elgg_view('object/elements/summary', $params);
