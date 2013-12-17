<?php

$entity = $vars["entity"];

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:content:description")));

echo "<div class='mvm'>" . elgg_view("input/longtext", array("name" => "content", "value" => $entity->content)) . "</div>";

echo "<div class='elgg-foot'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/submit", array("value" => elgg_echo("save")));
echo "</div>";