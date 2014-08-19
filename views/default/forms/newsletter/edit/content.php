<?php

$entity = $vars["entity"];

$id = "newsletter-edit-content-" . $entity->getGUID();

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:content:description")));

echo "<div class='mvm'>";
echo elgg_view("input/longtext", array("name" => "content", "value" => $entity->content, "id" => $id, "class" => "newsletter-input-plaintext"));
echo "</div>";

echo elgg_view("newsletter/placeholders");

echo "<div class='elgg-foot mtm'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/submit", array("value" => elgg_echo("save")));
echo "</div>";
