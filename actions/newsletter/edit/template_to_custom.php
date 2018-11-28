<?php

$guid = (int) get_input('guid');
$template = get_input('template');

if (empty($guid) || empty($template)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof Newsletter || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$entity->template = 'custom';

if (is_numeric($template)) {
	$template_entity = get_entity($template);
	if ($template_entity instanceof NewsletterTemplate) {
		$entity->html = $template_entity->html;
		$entity->css = $template_entity->css;
	}
} else {
	// assume the template is provided in a view
	if (elgg_view_exists('newsletter/templates/' . $template . '/css')) {
		$entity->css = elgg_view('newsletter/templates/' . $template . '/css');
	} else {
		// fallback to default CSS content
		$entity->css = elgg_view('newsletter/templates/default/css');
	}
	
	if (elgg_view_exists('newsletter/templates/' . $template . '/body')) {
		$entity->html = elgg_view('newsletter/templates/' . $template . '/body');
	} else {
		// fallback to default HTML content
		$entity->html = elgg_view('newsletter/templates/default/body');
	}
}

return elgg_ok_response('', elgg_echo('newsletter:action:template_to_custom:success'));
