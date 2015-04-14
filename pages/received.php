<?php
/**
 * Shows all the newsletters the user received
 */

gatekeeper();

$user = elgg_get_page_owner_entity();
if (empty($user) || !elgg_instanceof($user, "user")) {
	register_error(elgg_echo("pageownerunavailable", array(elgg_get_page_owner_guid())));
	forward(REFERER);
}

if (!$user->canEdit()) {
	register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	forward(REFERER);
}

// breadcrumb
elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:site"), "newsletter/site");
elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:received"));

// build page elements
if ($user->getGUID() == elgg_get_logged_in_user_guid()) {
	$title_text = elgg_echo("newsletter:received:title:mine");
} else {
	$title_text = elgg_echo("newsletter:received:title", array($user->name));
}

$options = array(
	"type" => "object",
	"subtype" => Newsletter::SUBTYPE,
	"full_view" => false,
	"metadata_name_value_pairs" => array(
		"name" => "status",
		"value" => "sent"
	),
	"relationship" => Newsletter::SEND_TO,
	"relationship_guid" => $user->getGUID(),
	"inverse_relationship" => true,
	"limit" => max(0, (int) get_input("limit", 10)),
	"offset" => max(0, (int) get_input("offset", 0)),
	"order_by_metadata" => array(
		"name" => "start_time",
		"as" => "integer",
		"direction" => "DESC"
	)
);

$ia = elgg_set_ignore_access(true);
$entities = elgg_get_entities_from_relationship($options);
elgg_set_ignore_access($ia);

if (!empty($entities)) {
	$content = elgg_view_entity_list($entities, $options);
} else {
	$content = elgg_view("output/longtext", array("value" => elgg_echo("notfound")));
}



// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $content,
	"filter" => ""
));

// draw page
echo elgg_view_page($title_text, $page_data);