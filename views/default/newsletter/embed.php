<?php

$entity = $vars["entity"];

$options = array(
		"type" => "object",
		"subtype" => "blog",
		"full_view" => false
	);

if ($entity->getContainerEntity() instanceof ElggGroup) {
	$options["container_guid"] = $entity->getContainerGUID();
}

$entities = elgg_get_entities($options);

echo "<ul id='newsletter-embed-list'>";
foreach($entities as $entity) {
	$description = $entity->description;
	
	echo "<li>";
	echo "<div>";
	echo "<strong>" . $entity->title . "</strong><br />";
	echo elgg_get_excerpt($description);
	echo "</div>";
	echo "<div class='newsletter-embed-item-content'>";
	
	echo "<strong>" . $entity->title . "</strong><br />";
	echo elgg_view("output/longtext", array("value" => $description));
	
	echo "</div></li>";
}

echo "</ul>";