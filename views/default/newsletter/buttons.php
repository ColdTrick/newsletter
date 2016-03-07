<?php

$messages = null;
if (count_messages()) {
	// get messages - try for errors first
	$messages = system_messages(NULL, 'error');
	if (count($messages['error']) == 0) {
		// no errors so grab rest of messages
		$messages = system_messages(null, '');
	} else {
		// we have errors - clear out remaining messages
		system_messages(null, '');
	}
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
elgg_load_css('newsletter_buttons');
