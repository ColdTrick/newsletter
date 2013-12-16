<?php

$newsletter = $vars["entity"];
$sections = $newsletter->sections;
if (!empty($sections)) {
	$sections = json_decode($sections, true);
}

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:content:description")));

echo "<form id='newsletter-section-list' class='mtm'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $newsletter->getGUID()));

if (!empty($sections)) {
	foreach($sections as $section) {
		echo elgg_view("newsletter/edit/elements/section", array("section" => $section));
	}
} else {
	echo elgg_view("newsletter/edit/elements/section");
}

echo elgg_view("output/url", array("href" => "#", "text" => "add section", "class" => "elgg-button elgg-button-action", "id" => "newsletter-section-add"));
echo elgg_view("newsletter/edit/elements/section", array("class" => "hidden"));

echo "</form>";