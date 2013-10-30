<?php

	$entity = elgg_extract("entity", $vars);
	$full_view = (bool) elgg_extract("full_view", $vars, false);
	
	$entity_menu = "";
	if (!elgg_in_context("widgets")) {
		$entity_menu = elgg_view_menu("entity", array(
			"entity" => $entity,
			"handler" => "newsletter",
			"sort_by" => "priority",
			"class" => "elgg-menu-hz"
		));
	}
	
	if ($full_view) {
		// full view
	} else {
		// listing view
		$icon = elgg_view_entity_icon($entity, "small");
		
		$excerpt = "";
		if ($entity->description) {
			$excerpt = elgg_get_excerpt($entity->description);
		}
		
		$params = array(
			"entity" => $entity,
			"metadata" => $entity_menu,
			"content" => $excerpt
		);
		
		$params = $params + $vars;
		$list_body = elgg_view("object/elements/summary", $params);
		
		echo elgg_view_image_block($icon, $list_body);
	}