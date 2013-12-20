<?php

$entity = $vars["entity"];
$offset = (int) max(get_input("offset", 0), 0);
$limit = 10;

$options = array(
		"type" => "object",
		"subtype" => "blog",
		"full_view" => false,
		"limit" => $limit,
		"offset" => $offset,
		"count" => true
	);

if ($entity->getContainerEntity() instanceof ElggGroup) {
	$options["container_guid"] = $entity->getContainerGUID();
}

$count = elgg_get_entities($options);

unset($options["count"]);

if ($count > 0) {
	$entities = elgg_get_entities($options);

	echo "<ul id='newsletter-embed-list'>";
	foreach($entities as $entity) {
		$description = $entity->description;
		
		echo "<li>";
		echo "<div>";
		echo "<strong>" . $entity->title . "</strong> ";
		echo elgg_get_excerpt($description);
		echo "</div>";
		echo "<div class='newsletter-embed-item-content'>";
		
		echo "<strong>" . $entity->title . "</strong><br />";
		echo elgg_view("output/longtext", array("value" => $description));
		
		echo "</div></li>";
	}
	
	echo "</ul>";
	
	echo "<div id='newsletter-embed-pagination'>";
	echo elgg_view("navigation/pagination", array("offset" => $offset, "limit" => $limit, "count" => $count));
	echo "</div>";
} else {
	echo elgg_echo("notfound");
}