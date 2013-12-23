<?php

/**
 * (un)subscribe to the newsletter of the container entity
 * This is shown in the sidebar
 *
 * @uses	$vars['entity']	The container entity to (un)subscribe to/from
 */

$entity = elgg_extract("entity", $vars);
$display_subbox = false;

if (elgg_instanceof($entity, "group")) {
	if (newsletter_is_group_enabled($entity)) {
		$display_subbox = true;
	}
} else {
	$display_subbox = true;
}

if ($display_subbox) {
	$title = elgg_echo("newsletter:sidebar:subscribe:title");
	
	$content = elgg_view_form("newsletter/subscribe", array(), array("entity" => $entity));
	
	echo elgg_view_module("aside", $title, $content);
}