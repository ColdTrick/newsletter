<?php

$entity = elgg_extract("entity", $vars);

if (!empty($entity)) {
	$title = elgg_get_sticky_value("newsletter_edit", "title", $entity->title);
	$subject = elgg_get_sticky_value("newsletter_edit", "subject", $entity->subject);
	$description = elgg_get_sticky_value("newsletter_edit", "description", $entity->description);
	$access_id = (int) elgg_get_sticky_value("newsletter_edit", "access_id", $entity->access_id);
	$tags = elgg_get_sticky_value("newsletter_edit", "tags", $entity->tags);
	
	echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
	
	$container_guid = $entity->container_guid;
} else {
	$title = elgg_get_sticky_value("newsletter_edit", "title");
	$subject = elgg_get_sticky_value("newsletter_edit", "subject");
	$description = elgg_get_sticky_value("newsletter_edit", "description");
	$access_id = (int) elgg_get_sticky_value("newsletter_edit", "access_id", get_default_access());
	$tags = elgg_get_sticky_value("newsletter_edit", "tags");
	
	$container_guid = (int) elgg_extract("container_guid", $vars);
}

elgg_clear_sticky_form("newsletter_edit");

echo "<div>";
echo "<label for='newsletter-title'>" . elgg_echo("title") . "</label>";
echo elgg_view("input/text", array("name" => "title", "value" => $title, "id" => "newsletter-title"));
echo "</div>";

echo "<div>";
echo "<label for='newsletter-subject'>" . elgg_echo("newsletter:edit:subject") . "</label>";
echo elgg_view("input/text", array("name" => "subject", "value" => $subject, "id" => "newsletter-subject"));
echo "</div>";

echo "<div>";
echo "<label for='newsletter-description'>" . elgg_echo("description") . "</label>";
echo elgg_view("input/longtext", array("name" => "description", "value" => $description, "id" => "newsletter-description"));
echo "</div>";

echo "<div>";
echo "<label for='newsletter-tags'>" . elgg_echo("tags") . "</label>";
echo elgg_view("input/tags", array("name" => "tags", "value" => $tags, "id" => "newsletter-tags"));
echo "</div>";

echo "<div>";
echo "<label for='newsletter-access-id'>" . elgg_echo("access") . "</label>";
echo elgg_view("input/access", array("name" => "access_id", "value" => $access_id, "id" => "newsletter-access-id", "class" => "mls"));
echo "</div>";

echo "<div class='elgg-foot'>";
echo elgg_view("input/hidden", array("name" => "container_guid", "value" => $container_guid));
echo elgg_view("input/submit", array("value" => elgg_echo("save")));
echo "</div>";