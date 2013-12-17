<?php

$section = elgg_extract("section", $vars, array("title" => "", "description" => ""));
$children = elgg_extract("children", $section, false);

$class = elgg_extract("class", $vars);

if (!empty($class)) {
	$class .= " ";
}
$class .= "newsletter-section";

echo "<div class='" . $class ."'>";
echo "<table><tr><td class='newsletter-section-drag'>";

echo elgg_view_icon("cursor-drag-arrow");

echo "</td><td>";
echo elgg_view("input/text", array("name" => "title", "value" => $section["title"], "placeholder" => elgg_echo("title")));

echo "</td><td class='newsletter-section-actions'>";

echo elgg_view("output/url", array("text" => elgg_echo("newsletter:edit:content:section:expand"), "class" => "newsletter-actions-edit")) . " " . elgg_view_icon("delete-alt");

echo "</td></tr><tr><td colspan='3'>";

echo elgg_view("input/plaintext", array("name" => "description", "value" => $section["description"], "placeholder" => elgg_echo("description"), "class" => "hidden"));

echo "</td></tr>";
echo "</table>";

// echo "<div class='newsletter-sub-section'>";
// if ($children) {
// 	foreach ($children as $child) {
// 		echo elgg_view("newsletter/edit/elements/section", array("section" => $child));
// 	}
// }

// echo "</div>";
echo "</div>";