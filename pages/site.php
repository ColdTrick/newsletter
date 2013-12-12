<?php

	/**
	 * View all the site newsletters
	 */

	// breadcrumb
	elgg_push_breadcrumb(elgg_echo("newsletter:breadcrumb:site"));
	
	// register title button
	if (elgg_is_admin_logged_in()) {
		elgg_register_title_button();
	}
	
	// build page elements
	$title_text = elgg_echo("newsletter:site:title");
	
	$options = array(
		"type" => "object",
		"subtype" => Newsletter::SUBTYPE,
		"container_guid" => elgg_get_site_entity()->getGUID(),
		"full_view" => false
	);
	
	if (!($content = elgg_list_entities($options))) {
		$content = elgg_view("output/longtext", array("value" => elgg_echo("notfound")));
	}
	
	$sidebar = elgg_view("newsletter/sidebar/subscribe", array("entity" => elgg_get_site_entity()));
	
	// build page
	$page_data = elgg_view_layout("content", array(
		"title" => $title_text,
		"content" => $content,
		"sidebar" => $sidebar,
		"filter" => ""
	));
	
	// draw page
	echo elgg_view_page($title_text, $page_data);