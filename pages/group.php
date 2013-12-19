<?php

/**
 * List all the newsletter in this group
 *
 * @uses elgg_get_page_owner_entity() the group to list the newsletter from
 */

$page_owner = elgg_get_page_owner_entity();
if (empty($page_owner) || !elgg_instanceof($page_owner, "group")) {
	register_error(elgg_echo("pageownerunavailable", array(elgg_get_page_owner_guid())));
	forward(REFERER);
}

if (!newsletter_is_group_enabled($page_owner)) {
	register_error(elgg_echo("newsletter:group:error:not_enabled"));
	forward($page_owner->getURL());
}

// build breadcrumb
elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:site"), "newsletter/site");
elgg_push_breadcrumb($page_owner->name);

// build page elements
if ($page_owner->canEdit()) {
	elgg_register_title_button();
}

$title_text = elgg_echo("newsletter:group:title", array($page_owner->name));

$options = array(
	"type" => "object",
	"subtype" => Newsletter::SUBTYPE,
	"container_guid" => $page_owner->getGUID(),
	"full_view" => false
);

if (!($content = elgg_list_entities($options))) {
	$content = elgg_view("output/longtext", array("value" => elgg_echo("notfound")));
}

$sidebar = elgg_view("newsletter/sidebar/subscribe", array("entity" => $page_owner));

// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $content,
	"sidebar" => $sidebar,
	"filter" => ""
));

// draw page
echo elgg_view_page($title_text, $page_data);
