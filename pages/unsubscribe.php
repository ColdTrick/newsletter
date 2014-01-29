<?php

/**
 * To unsubscribe from the newsletter of a container (and all others)
 *
 * @uses elgg_get_page_onwer_entity() from which container to unsubscribe primairly
 */

$guid = (int) get_input("guid");
$code = get_input("c");
$user_guid = (int) get_input("u");
$email = get_input("e");

// check if we have correct input
if (empty($guid)) {
	register_error(elgg_echo("newsletter:unsubscribe:error:input"));
	forward();
}

$entity = get_entity($guid);
if (!elgg_instanceof($entity, "site") && !elgg_instanceof($entity, "group")) {
	forward();
}

if (!empty($user_guid)) {
	$recipient = $user_guid;
} else {
	$recipient = $email;
}

// validate validation code
if ($code && !newsletter_validate_unsubscribe_code($entity, $recipient, $code)) {
	register_error(elgg_echo("newsletter:unsubscribe:error:code"));
	forward();
}
	
// breadcrumb
elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:site"), "newsletter/site");
if (elgg_instanceof($entity, "group")) {
	elgg_push_breadcrumb($entity->name, "newsletter/group/" . $entity->getGUID());
}
elgg_push_breadcrumb(elgg_echo("newsletter:unsubscribe"));

// build page elements
$title_text = elgg_echo("newsletter:unsubscribe:title");

$form = elgg_view_form("newsletter/unsubscribe", array(), array("entity" => $entity, "recipient" => $recipient, "code" => $code));

// build page
$page_data = elgg_view_layout("content", array(
	"title" => $title_text,
	"content" => $form,
	"filter" => ""
));

// draw page
echo elgg_view_page($title_text, $page_data);
