<?php

$template = $vars["entity"]->template;
if (empty($template)) {
	$template = "default";
}

if ($template == "custom") {
	$css = $vars["entity"]->css;
} else {
	$css = elgg_view("newsletter/templates/" . $template . "/css");
}

echo $css;
