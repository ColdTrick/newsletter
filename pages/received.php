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
	"inverse_relationship" => true
);

$ia = elgg_set_ignore_access(true);

if (!($content = elgg_list_entities_from_relationship($options))) {
	$content = elgg_view("output/longtext", array("value" => elgg_echo("notfound")));
}

elgg_set_ignore_access($ia);

// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $content,
	"filter" => ""
));

// draw page
echo elgg_view_page($title_text, $page_data);