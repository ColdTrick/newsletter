<?php
$guid = (int) get_input('guid');
$template = get_input('template');

if (empty($guid) || empty($template)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}
	
$entity->template = 'custom';

if (is_numeric($template)) {
	$template_entity = get_entity($template);
	if (!empty($template_entity) && elgg_instanceof($template_entity, 'object', NEWSLETTER_TEMPLATE)) {
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

system_message(elgg_echo('newsletter:action:template_to_custom:success'));

forward(REFERER);
