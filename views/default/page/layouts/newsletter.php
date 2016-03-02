<?php

/**
 * A layout for the newsletter, to be used in preview, online and mail
 *
 * @uses $vars['entity'] 	The newsletter to be viewed
 */

$language = get_current_language();

$entity = elgg_extract('entity', $vars);

$head = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
$head .= '<base target="_blank" />';
$head .= elgg_format_element('title', [], $entity->title);

$body = elgg_format_element('style', ['type' => 'text/css'], elgg_view('newsletter/view/css', $vars));
$body .= elgg_view('newsletter/view/body', $vars);

echo elgg_view('page/elements/html', ['head' => $head, 'body' => $body]);
