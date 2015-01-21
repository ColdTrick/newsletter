<?php

$entity = elgg_extract("entity", $vars);
if (empty($entity) || !elgg_instanceof($entity, "object")) {
	return;
}
$page_owner = elgg_extract("page_owner", $vars);
$container = $entity->getContainerEntity();

$data = array(
	"class" => "newsletter-embed-item-data",
	"data-title" => $entity->title,
	"data-description" => $entity->description,
	"data-url" => $entity->getURL()
);

$subtitle = array(
	elgg_echo("item:object:" . $entity->getSubtype()),
	elgg_echo("by") . " " . $entity->getOwnerEntity()->name
);
if (elgg_instanceof($container, "group")) {
	if (!empty($page_owner) && ($container->getGUID() != $page_owner->getGUID())) {
		$subtitle[] = elgg_echo("river:ingroup", array($container->name));
	}
}

$params = array(
	"entity" => $entity,
	"title" => $entity->title,
	"subtitle" => implode(" ", $subtitle),
	"tags" => false,
	"content" => elgg_get_excerpt($entity->description)
);

echo elgg_view("object/elements/summary", $params);
echo "<div " . elgg_format_attributes($data) . "></div>";