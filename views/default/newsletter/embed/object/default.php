<?php

$entity = elgg_extract("entity", $vars);
if (empty($entity) || !elgg_instanceof($entity, "object")) {
	return;
}
$newsletter = elgg_extract("newsletter", $vars);
$container = $entity->getContainerEntity();

// data for embedding
$data = array(
	"data-title" => $entity->title,
	"data-description" => $entity->description,
	"data-url" => $entity->getURL()
);

// excerpt support
$excerpt = $entity->excerpt;
if (empty($excerpt)) {
	$excerpt = elgg_get_excerpt($entity->description);
}
if (!empty($excerpt)) {
	$data["data-excerpt"] = $excerpt;
}

// icon support
if ($entity->icontime) {
	$data["data-icon-url"] = $entity->getIconURL("large");
}

// subtitle
$subtitle = array(
	elgg_echo("item:object:" . $entity->getSubtype()),
	elgg_echo("by") . " " . $entity->getOwnerEntity()->name
);
if (elgg_instanceof($container, "group")) {
	$subtitle[] = elgg_echo("river:ingroup", array($container->name));
}

// build listing view
$params = array(
	"entity" => $entity,
	"title" => $entity->title,
	"subtitle" => implode(" ", $subtitle),
	"tags" => false,
	"content" => elgg_get_excerpt($entity->description)
);

echo "<div " . elgg_format_attributes($data) . ">";
echo elgg_view("object/elements/summary", $params);
echo "</div>";
