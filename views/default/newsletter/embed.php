<?php

$entity = $vars["entity"];
$offset = (int) max(get_input("offset", 0), 0);
$limit = 10;

$query = get_input("q");
$query = sanitise_string($query);

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

if (!empty($query)) {
	$dbprefix = elgg_get_config("dbprefix");
	$options["joins"] = array("JOIN " . $dbprefix . "objects_entity oe ON e.guid = oe.guid");
	$options["wheres"] = array("(oe.title LIKE '%" . $query . "%')");
}

$count = elgg_get_entities($options);

unset($options["count"]);

if ($count > 0) {
	$entities = elgg_get_entities($options);
	
	$form_data = elgg_view("input/text", array("name" => "q", "value" => $query));
	$form_data .= elgg_view("input/submit", array("value" => elgg_echo("search"), "class" => "elgg-button-action"));
	
	echo elgg_view("input/form", array("action" => "newsletter/embed/" . $entity->getGUID(), "id" => "newsletter-embed-search", "body" => $form_data));
	
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
		
		if ($entity->icontime) {
			$description = elgg_view("output/img", array("src" => $entity->getIconURL("large"), "alt" => $entity->title, "style" => "float: left; margin: 5px;")) . $description;
		}
		
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