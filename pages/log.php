<?php
/**
 * View the logging of a newsletter
 *
 * @uses get_input("guid") the GUID of the newsletter to view
 */

gatekeeper();

$guid = (int) get_input("guid");

// validate input
if (empty($guid)) {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	forward(REFERER);
}

$entity = get_entity($guid);
if (empty($entity) || !$entity->canEdit()) {
	register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	forward(REFERER);
}

if (!elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
	register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
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
elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:log"));

// build page elements
$title_text = elgg_echo("newsletter:log:title", array($entity->title));

$content = elgg_view("newsletter/log", array("entity" => $entity));

// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $content,
	"filter" => ""
));

// draw page
echo elgg_view_page($title_text, $page_data);
