<?php

$replacements = [
	'{content}' => elgg_echo('newsletter:placeholders:content'),
	'{header}' => elgg_echo('newsletter:placeholders:header'),
	'{unsub}' => elgg_echo('newsletter:placeholders:unsub'),
	'{unsublink}' => elgg_echo('newsletter:placeholders:unsublink'),
	'{online}' => elgg_echo('newsletter:placeholders:online'),
	'{title}' => elgg_echo('newsletter:placeholders:title'),
	'{description}' => elgg_echo('newsletter:placeholders:description'),
	'{subject}' => elgg_echo('newsletter:placeholders:subject'),
	'{newsletter_url}' => elgg_echo('newsletter:placeholders:newsletter_url'),
	'{site_name}' => elgg_echo('newsletter:placeholders:site_name'),
	'{site_description}' => elgg_echo('newsletter:placeholders:site_description'),
	'{site_url}' => elgg_echo('newsletter:placeholders:site_url'),
	'{container_name}' => elgg_echo('newsletter:placeholders:container_name'),
	'{container_url}' => elgg_echo('newsletter:placeholders:container_url'),
];

echo elgg_echo('newsletter:placeholders:info');

$list_items = [];
foreach ($replacements as $replacement => $info) {
	$list_items[] = elgg_format_element('li', ['title' => $info], $replacement);
}

echo elgg_format_element('ul', ['class' => 'newsletter-placeholders'], implode(PHP_EOL, $list_items));
