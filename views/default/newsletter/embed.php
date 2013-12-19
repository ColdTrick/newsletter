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
	echo "<li><p>" . $entity->title . "</p></li>";
}

echo "</ul>";