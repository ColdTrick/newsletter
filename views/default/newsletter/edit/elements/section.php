<?php

$section = elgg_extract("section", $vars, array("title" => "", "description" => ""));

$class = elgg_extract("class", $vars);

if (!empty($class)) {
	$class .= " ";
}
$class .= "newsletter-section";

echo "<div class='" . $class ."'>";
echo "<table><tr><td>";
echo elgg_view("input/text", array("name" => "title", "value" => $section["title"], "placeholder" => elgg_echo("title")));

echo elgg_view("input/plaintext", array("name" => "description", "value" => $section["description"], "placeholder" => elgg_echo("description")));
echo "</td><td class='newsletter-section-actions'>";

echo elgg_view_icon("cursor-drag-arrow") . "<br />" . elgg_view_icon("delete-alt");

echo "</td></tr></table>";
echo "</div>";