<?php

gatekeeper();

$page_owner = elgg_get_page_owner_entity();
if (empty($page_owner) || !elgg_instanceof($page_owner, "user") || !$page_owner->canEdit()) {
	register_error(elgg_echo("noaccess"));
	forward(REFERER);
}

// set context
$old_context = elgg_pop_context();
elgg_push_context("settings");

// set breadcrumb
elgg_push_breadcrumb(elgg_echo("settings"), "settings/user/" . $page_owner->username);
elgg_push_breadcrumb(elgg_echo("newsletter:menu:page:settings"));

// build page element
$title_text = elgg_echo("newsletter:menu:page:settings");

$content = elgg_view_form("newsletter/subscriptions", array("id" => "newsletter-settings-form"), array("entity" => $page_owner));

// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $content,
	"filter" => ""
));

// restore context
elgg_pop_context();
elgg_push_context($old_context);

// draw page
echo elgg_view_page($title_text, $page_data);