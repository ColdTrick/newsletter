<?php

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:content:description")));

echo "<div id='newsletter-section-list' class='mtm'>";

echo elgg_view("newsletter/edit/elements/section");
echo elgg_view("newsletter/edit/elements/section");
echo elgg_view("newsletter/edit/elements/section");

echo elgg_view("output/url", array("href" => "#", "text" => "add section", "class" => "elgg-button elgg-button-action", "id" => "newsletter-section-add"));
echo elgg_view("newsletter/edit/elements/section", array("class" => "hidden"));

echo "</div>";