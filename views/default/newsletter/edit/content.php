<?php

$newsletter = $vars["entity"];
$sections = $newsletter->sections;
if (!empty($sections)) {
	$sections = json_decode($sections, true);
}

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:content:description")));

echo "<div class='clearfix'>";
echo "<div class='float-alt'>";
echo elgg_view("output/url", array("text" => elgg_echo("newsletter:edit:content:section:expand_all"), "href" => "#", "class" => "newsletter-expand-all")) . " | ";
echo elgg_view("output/url", array("text" => elgg_echo("newsletter:edit:content:section:collapse_all"), "href" => "#", "class" => "newsletter-collapse-all"));
echo "</div>";
echo "</div>";

echo "<form id='newsletter-section-list' class='mtm'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $newsletter->getGUID()));

if (!empty($sections)) {
	foreach($sections as $section) {
		echo elgg_view("newsletter/edit/elements/section", array("section" => $section));
	}
} else {
	echo elgg_view("newsletter/edit/elements/section");
}

echo elgg_view("output/url", array("href" => "#", "text" => elgg_echo("newsletter:edit:content:add_section"), "class" => "elgg-button elgg-button-action mtm", "id" => "newsletter-section-add"));
echo elgg_view("newsletter/edit/elements/section", array("class" => "hidden"));

echo "</form>";