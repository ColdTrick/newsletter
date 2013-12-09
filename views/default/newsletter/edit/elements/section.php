<?php

$class = elgg_extract("class", $vars);

if (!empty($class)) {
	$class .= " ";
}
$class .= "newsletter-section";

echo "<div class='" . $class ."'>";
echo "<table><tr><td>";
echo elgg_view("input/text", array("name" => "section_title", "placeholder" => elgg_echo("title")));

echo elgg_view("input/plaintext", array("name" => "section_description", "placeholder" => elgg_echo("description")));
echo "</td><td class='newsletter-section-actions'>";

echo elgg_view_icon("cursor-drag-arrow") . "<br />" . elgg_view_icon("delete-alt");

echo "</td></tr></table>";
echo "</div>";