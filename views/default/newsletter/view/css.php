<?php

$template = $vars["entity"]->template;
if ($template == "custom") {
	$css = $vars["entity"]->styling_css;
} else {
	$css = elgg_view("newsletter/templates/" . $template . "/css");
}

echo $css;
