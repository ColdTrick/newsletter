<?php

echo "<div class='hidden' id='newsletter-embed-format'>";

echo "<fieldset>";

echo "<div>";
echo "<label>" . elgg_echo("Show description") . "</label>";
echo elgg_view("input/select", array(
	"options_values" => array(
		"full" => elgg_echo("full"),
		"excerpt" => elgg_echo("excerpt"),
		"no" => elgg_echo("option:no")
	),
	"name" => "newsletter-embed-format-description",
	"class" => "mlm"
));
echo "</div>";

echo "<div id='newsletter-embed-format-icon'>";

echo "<label>" . elgg_echo("Show icon") . "</label>";
echo elgg_view("input/select", array(
	"options_values" => array(
		"left" => elgg_echo("align left"),
		"right" => elgg_echo("align right"),
		"none" => elgg_echo("do not show icon")
	),
	"name" => "newsletter-embed-format-icon",
	"class" => "mlm"
));
		
echo "</div>";

echo "</fieldset>";

echo elgg_view("input/button", array(
	"class" => "elgg-button-submit",
	"value" => elgg_echo("newsletter:embed:format:add_to_newsletter")
));

echo "<div id='newsletter-embed-format-preview'>";

echo "</div>";

echo "</div>";