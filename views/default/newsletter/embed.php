<?php

$newsletter = elgg_extract("entity", $vars);
$offset = (int) max(get_input("offset", 0), 0);
$limit = 6;

$query = get_input("q");
$query = sanitise_string($query);

$dbprefix = elgg_get_config("dbprefix");

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
	"count" => true,
	"wheres" => array()
);

$container = $newsletter->getContainerEntity();
if (empty($show_all) && elgg_instanceof($container, "group")) {
	$container_guid = $newsletter->getContainerGUID();
	
	if (elgg_is_active_plugin("static")) {
		// static subpages do not have a group container so do an extra check
		$options["wheres"][] = "
			((e.container_guid = {$container_guid}) OR e.guid IN (
				SELECT sub_r.guid_one from {$dbprefix}entity_relationships sub_r
				JOIN {$dbprefix}entities sub_e ON sub_e.guid = sub_r.guid_two
				WHERE sub_e.container_guid = {$container_guid}
				AND sub_r.relationship = 'subpage_of'
			))";
	} else {
		$options["container_guid"] = $container_guid;
	}
}

if (!empty($query)) {
	$options["joins"] = array("JOIN " . $dbprefix . "objects_entity oe ON e.guid = oe.guid");
	$options["wheres"][] = "(oe.title LIKE '%" . $query . "%')";
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

echo "<div id='newsletter-embed-wrapper'>";
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
		echo newsletter_view_embed_content($entity, array("newsletter" => $newsletter));
		echo "</li>";
	}
	
	echo "</ul>";
	
	echo "<div id='newsletter-embed-pagination'>";
	
	$show_all_value = $show_all ? 1 : 0;
	
	echo elgg_view("navigation/pagination", array(
		"base_url" => elgg_normalize_url("newsletter/embed/" . $newsletter->getGUID() . "?q=$query&show_all=$show_all_value"),
		"offset" => $offset,
		"limit" => $limit,
		"count" => $count
	));
	echo "</div>";
} else {
	echo elgg_echo("notfound");
}

echo "</div>";
if ($count > 0) {
	echo elgg_view("newsletter/format");
}
