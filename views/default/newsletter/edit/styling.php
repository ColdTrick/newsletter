<?php

$newsletter = $vars["entity"];

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:styling:description")));

$form_data = "<div><label>" . elgg_echo("html") . "<br />";
$form_data .= elgg_view("input/plaintext", array("name" => "html", "value" => $newsletter->styling_html));
$form_data .= "</label></div>";

$form_data .= "<div><label>" . elgg_echo("css") . "<br />";
$form_data .= elgg_view("input/plaintext", array("name" => "css", "value" => $newsletter->styling_css));
$form_data .= "</label></div>";

$form_data .= elgg_view("input/hidden", array("name" => "guid", "value" => $newsletter->getGUID()));
$form_data .= elgg_view("input/submit", array("value" => elgg_echo("save")));

echo elgg_view("input/form", array("class" => "mtm", "id" => "newsletter-styling", "action" => "action/newsletter/edit/styling", "body" => $form_data));
