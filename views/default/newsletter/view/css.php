<?php

$template = $vars["entity"]->template;
if ($template == "custom") {
	$css = $vars["entity"]->css;
} else {
	$css = elgg_view("newsletter/templates/" . $template . "/css");
}

echo $css;
