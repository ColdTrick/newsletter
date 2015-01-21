<?php

$newsletter = elgg_extract("entity", $vars);
$offset = (int) max(get_input("offset", 0), 0);
$limit = 6;

$query = get_input("q");
$query = sanitise_string($query);

$show_all = (bool) get_input("show_all", false);

$subtypes = array();
if (elgg_is_active_plugin("blog")) {
	$subtypes[] = "blog";
}
if (elgg_is_active_plugin("static")) {
	$subtypes[] = "static";
}

if (empty($subtypes)) {
	return;
}

$options = array(
	"type" => "object",
	"subtypes" => $subtypes,
	"full_view" => false,
	"limit" => $limit,
	"offset" => $offset,
	"count" => true
);

$container = $newsletter->getContainerEntity();
if (empty($show_all) && elgg_instanceof($container, "group")) {
	$options["container_guid"] = $newsletter->getContainerGUID();
}

if (!empty($query)) {
	$dbprefix = elgg_get_config("dbprefix");
	$options["joins"] = array("JOIN " . $dbprefix . "objects_entity oe ON e.guid = oe.guid");
	$options["wheres"] = array("(oe.title LIKE '%" . $query . "%')");
}

$count = elgg_get_entities($options);
unset($options["count"]);

// search form
$form_data = elgg_view("input/text", array("name" => "q", "value" => $query));
$form_data .= elgg_view("input/submit", array("value" => elgg_echo("search"), "class" => "elgg-button-action"));

if (elgg_instanceof($container, "group")) {
	$form_data .= "<div class='mts'>";
	$form_data .= elgg_view("input/checkbox", array(
		"name" => "show_all",
		"value" => "1",
		"checked" => $show_all,
		"default" => false
	));
	$form_data .= elgg_echo("newsletter:embed:show_all");
	$form_data .= "</div>";
}

echo elgg_view("input/form", array(
	"action" => "newsletter/embed/" . $newsletter->getGUID(),
	"id" => "newsletter-embed-search",
	"body" => $form_data,
	"disable_security" => true
));

if ($count > 0) {
	$entities = elgg_get_entities($options);
	
	echo "<ul id='newsletter-embed-list'>";
	foreach ($entities as $entity) {
		
		echo "<li class='newsletter-embed-item'>";
		echo newsletter_view_embed_content($entity, array("page_owner" => $container));
		echo "</li>";
	}
	
	echo "</ul>";
	
	echo "<div id='newsletter-embed-pagination'>";
	echo elgg_view("navigation/pagination", array(
		"base_url" => elgg_normalize_url("newsletter/embed/" . $newsletter->getGUID()),
		"offset" => $offset,
		"limit" => $limit,
		"count" => $count
	));
	echo "</div>";
} else {
	echo elgg_echo("notfound");
}