<?php

$entity = elgg_extract('entity', $vars);
$template = $entity->template;
if (empty($template)) {
	$template = 'default';
}

if (is_numeric($template)) {
	// probably a custom template, lets check
	$template_entity = get_entity($template);

	if ($template_entity instanceof NewsletterTemplate) {
		$css = $template_entity->css;
	} else {
		// something wrong, reset to default
		$template = 'default';
	}
}

if (!isset($css)) {
	if ($template == 'custom') {
		$css = $entity->css;
	} else {
		if (!elgg_view_exists("newsletter/templates/{$template}/css")) {
			// something wrong, reset to default
			$template = 'default';
		}
		
		$css = elgg_view("newsletter/templates/{$template}/css");
	}
}

echo $css;
