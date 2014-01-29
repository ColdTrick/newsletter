<?php

$template = $vars["entity"]->template;
if (empty($template)) {
	$template = "default";
}

if (is_numeric($template)) {
	// probably a custom template, lets check
	$template_entity = get_entity($template);

	if ($template_entity && ($template_entity->getSubtype() == NEWSLETTER_TEMPLATE)) {
		$css = $template_entity->css;
	} else {
		// something wrong, reset to default
		$template = "default";
	}
}

if (!isset($css)) {
	if ($template == "custom") {
		$css = $vars["entity"]->css;
	} else {
		if (!elgg_view_exists("newsletter/templates/" . $template . "/css")) {
			// something wrong, reset to default
			$template = "default";
		}
		
		$css = elgg_view("newsletter/templates/" . $template . "/css");
	}
}

echo $css;
