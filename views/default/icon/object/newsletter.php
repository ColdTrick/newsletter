<?php
$entity = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars, 'medium');
$icon_sizes = elgg_get_config('icon_sizes');

if (!array_key_exists($size, $icon_sizes)) {
	$size = 'medium';
}

$size_config = elgg_extract($size, $icon_sizes);
$width = elgg_extract('w', $size_config);
$height = elgg_extract('h', $size_config);

$font_size = floor($height * 0.75);

$icon = elgg_view_icon('newspaper-o', [
	'style' => "width: {$width}px; height: {$height}px; font-size: {$font_size}px; line-height: {$height}px;",
	'class' => 'center',
]);
echo elgg_view('output/url', [
	'text' => $icon,
	'href' => $entity->getURL(),
]);