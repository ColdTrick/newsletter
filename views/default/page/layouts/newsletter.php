<?php
/**
 * A layout for the newsletter, to be used in preview, online and mail
 *
 * @uses $vars['entity'] The newsletter to be viewed
 */

// override image service to be able to disable the webp generation of images as that is not supported by all email clients
$imagine = new \Imagine\Gd\Imagine();
if (elgg_get_config('image_processor') === 'imagick' && extension_loaded('imagick')) {
	$imagine = new \Imagine\Imagick\Imagine();
}

$image_service = new \ColdTrick\Newsletter\ImageService($imagine, _elgg_services()->config, _elgg_services()->mimetype);

_elgg_services()->set('imageService', $image_service);
_elgg_services()->reset('iconService');

/* @var $entity Newsletter */
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

echo elgg_view('page/elements/html', ['head' => $head, 'body' => $body]);

// reset the image service
_elgg_services()->reset('imageService');
_elgg_services()->reset('iconService');
