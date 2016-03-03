<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view('output/text', ['value' => elgg_echo('newsletter:edit:content:description')]);

$content_input = elgg_view('input/longtext', [
	'name' => 'content',
	'value' => $entity->content,
	'id' => "newsletter-edit-content-{$entity->getGUID()}",
	'class' => 'newsletter-input-plaintext',
]);
echo elgg_format_element('div', ['class' => 'mvm'], $content_input);

echo elgg_view('newsletter/placeholders');

$foot = elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);
$foot .= elgg_view('input/submit', ['value' => elgg_echo('save')]);
echo elgg_format_element('div', ['class' => 'elgg-foot mtm'], $foot);
