<?php
/**
 * A layout for the newsletter, to be used in preview, online and mail
 *
 * @uses $vars['entity'] The newsletter to be viewed
 */

// disable the webp generation of images as that is not supported by all email clients
$webp_config = elgg_get_config('webp_enabled');
elgg_set_config('webp_enabled', false);

/* @var $entity \Newsletter */
$entity = elgg_extract('entity', $vars);

$head = elgg_format_element('meta', [
	'http-equiv' => 'Content-Type',
	'content' => 'text/html; charset=UTF-8',
]);
$head .= elgg_format_element('base', [
	'target' => '_blank',
]);
$head .= elgg_format_element('title', [], $entity->getDisplayName());

$css = elgg_view('newsletter/view/css', $vars);
$body = elgg_view('newsletter/view/body', $vars);

// doing this here instead of during notification as it is the same for each recipient
$body = elgg()->html_formatter->inlineCss($body, $css);

// restore webp support
elgg_set_config('webp_enabled', $webp_config);

// draw newsletter
echo elgg_view('page/elements/html', [
	'head' => $head,
	'body' => $body,
]);
