<?php

/**
 * View all the site newsletters
 */

$filter = false;

// breadcrumb
elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:site"));

// register title button
if (elgg_is_admin_logged_in()) {
	elgg_register_title_button();
	
	$filter = get_input("filter");
	
	if (empty($filter)) {
		$filter = "concept";
		// input is used in filter tabs also
		set_input("filter", $filter);
	}
}

// build page elements
$title_text = elgg_echo("newsletter:site:title");

$options = array(
	"type" => "object",
	"subtype" => Newsletter::SUBTYPE,
	"container_guid" => elgg_get_site_entity()->getGUID(),
	"full_view" => false
);

switch ($filter) {
	case "concept":
		$options["metadata_name_value_pairs"] = array(
			"name" => "status",
			"value" => "concept"
		);
		break;
	case "scheduled":
		$options["metadata_name_value_pairs"] = array(
			"name" => "status",
			"value" => "scheduled"
		);
		$options["order_by_metadata"] = array(
			"name" => "scheduled",
			"as" => "integer"
		);
		break;
	case "sending":
		$options["metadata_name_value_pairs"] = array(
			"name" => "status",
			"value" => "sending"
		);
		break;
	case "sent":
	default:
		$options["metadata_name_value_pairs"] = array();
		$options["metadata_name_value_pairs"][] = array(
			"name" => "status",
			"value" => "sent"
		);
		if (!elgg_is_admin_logged_in()) {
			$options["metadata_name_value_pairs"][] = array(
				"name" => "show_in_archive",
				"value" => 1
			);
		}
		
		$options["order_by_metadata"] = array(
			"name" => "start_time",
			"as" => "integer",
			"direction" => "DESC"
		);
		break;
}

if (!($content = elgg_list_entities_from_metadata($options))) {
	$content = elgg_view("output/longtext", array("value" => elgg_echo("notfound")));
}

$sidebar = elgg_view("newsletter/sidebar/subscribe", array("entity" => elgg_get_site_entity()));

// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $content,
	"sidebar" => $sidebar,
));

// draw page
echo elgg_view_page($title_text, $page_data);
