<?php

echo "<div class='hidden' id='newsletter-embed-format'>";

echo "<fieldset>";

echo "<div>";
echo "<label>" . elgg_echo("newsletter:embed:format:description:title") . "</label>";
echo elgg_view("input/select", array(
	"options_values" => array(
		"full" => elgg_echo("newsletter:embed:format:description:option:full"),
		"excerpt" => elgg_echo("newsletter:embed:format:description:option:excerpt"),
		"no" => elgg_echo("newsletter:embed:format:description:option:no")
	),
	"name" => "newsletter-embed-format-description",
	"id" => "newsletter-embed-format-description",
	"class" => "mlm"
));
echo "</div>";

echo "<div>";

echo "<label>" . elgg_echo("newsletter:embed:format:icon:title") . "</label>";
echo elgg_view("input/select", array(
	"options_values" => array(
		"left" => elgg_echo("newsletter:embed:format:icon:option:left"),
		"right" => elgg_echo("newsletter:embed:format:icon:option:right"),
		"none" => elgg_echo("newsletter:embed:format:icon:option:none")
	),
	"name" => "newsletter-embed-format-icon",
	"id" => "newsletter-embed-format-icon",
	"class" => "mlm"
));
		
echo "</div>";

echo "</fieldset>";

echo elgg_view("input/button", array(
	"class" => "elgg-button-submit mvl",
	"value" => elgg_echo("newsletter:embed:format:add_to_newsletter"),
	"onclick" => "elgg.newsletter.embed_format_submit()"
));

$preview_title = elgg_echo("newsletter:embed:format:preview:title");
$preview_content = "<div id='newsletter-embed-format-preview'></div>";

echo elgg_view_module("info", $preview_title, $preview_content);

echo "</div>";