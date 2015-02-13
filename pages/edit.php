<?php

/**
 * Edit an existing newsletter
 *
 * @uses get_input("guid") the guid of the newsletter to edit
 */

elgg_gatekeeper();

elgg_require_js("newsletter/edit");

$guid = (int) get_input("guid");
$subpage = get_input("subpage");

// validate input
elgg_entity_gatekeeper($guid, "object", Newsletter::SUBTYPE);
$entity = get_entity($guid);
if (!$entity->canEdit()) {
	register_error(elgg_echo("limited_access"));
	forward(REFERER);
}

// set page owner
$container = $entity->getContainerEntity();
if (elgg_instanceof($container, "group")) {
	elgg_set_page_owner_guid($entity->getContainerGUID());
} else {
	elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
}

// breadcrumb
elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:site"), "newsletter/site");
if (elgg_instanceof($container, "group")) {
	elgg_push_breadcrumb($container->name, "newsletter/group/" . $container->getGUID());
}
elgg_push_breadcrumb($entity->title, $entity->getURL());
elgg_push_breadcrumb(elgg_echo("edit"));

// build page elements
$title_text = elgg_echo("newsletter:edit:title", array($entity->title));

$vars = array(
	"entity" => $entity
);

if ($subpage) {
	$form_vars = array(
		"id" => "newsletter-form-" . $subpage
	);
	
	if ($entity->content) {
		// only show preview if content available
		elgg_register_menu_item("title", ElggMenuItem::factory(array(
				"name" => "preview",
				"text" => elgg_echo("preview"),
				"href" => "newsletter/preview/" . $guid,
				"link_class" => "elgg-button elgg-button-action"
		)));
	}
		
	if ($subpage == "recipients") {
		$form_vars["enctype"] = "multipart/form-data";
	}
	
	$content = elgg_view_form("newsletter/edit/" . $subpage, $form_vars, $vars);
} else {
	$content = elgg_view("newsletter/edit", $vars);
}

$filter_tabs = elgg_view_menu("newsletter_steps", array(
	"entity" => $entity,
	"class" => "elgg-tabs",
	"sort_by" => "register"
));

// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $content,
	"filter" => $filter_tabs
));

// draw page
echo elgg_view_page($title_text, $page_data);