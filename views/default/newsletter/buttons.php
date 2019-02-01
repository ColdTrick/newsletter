<?php

$messages = null;
if (elgg()->system_messages->count()) {
	// get messages - try for errors first
	$messages = elgg()->system_messages->dumpRegister();
	echo elgg_format_element('style', ['type' => 'text/css'], elgg_view('elements/components/messages.css'));
}

$type = elgg_extract('type', $vars, 'view');
$entity = elgg_extract('entity', $vars);

echo elgg_view_menu('newsletter_buttons', [
	'entity' => $entity,
	'type' => $type,
	'class' => 'newsletter-buttons',
	'sort_by' => 'priority',
]);

echo elgg_view('page/elements/messages', ['object' => $messages]);

echo elgg_format_element('style', ['type' => 'text/css'], elgg_view('css/newsletter/buttons.css'));
