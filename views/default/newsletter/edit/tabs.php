<?php

	$entity = elgg_extract("entity", $vars);
	$subpage = elgg_extract("subpage", $vars, "entity");
	
	$entity_url = false;
	$content_url = false;
	$styling_url = false;
	$recipients_url = false;
	$schedule_url = false;
	
	if (!empty($entity)) {
		$entity_url = "newsletter/edit/" . $entity->getGUID();
		$content_url = "newsletter/edit/" . $entity->getGUID() . "/content";
		$styling_url = "newsletter/edit/" . $entity->getGUID() . "/styling";
		$recipients_url = "newsletter/edit/" . $entity->getGUID() . "/recipients";
		$schedule_url = "newsletter/edit/" . $entity->getGUID() . "/schedule";
	}
	
	$tabs = array(
		array(
			"text" => elgg_echo("newsletter:edit:tabs:entity"),
			"href" => $entity_url,
			"selected" => ($subpage == "entity") ? true : false
		),
		array(
			"text" => elgg_echo("newsletter:edit:tabs:content"),
			"href" => $content_url,
			"selected" => ($subpage == "content") ? true : false
		),
		array(
			"text" => elgg_echo("newsletter:edit:tabs:styling"),
			"href" => $styling_url,
			"selected" => ($subpage == "styling") ? true : false
		),
		array(
			"text" => elgg_echo("newsletter:edit:tabs:recipients"),
			"href" => $recipients_url,
			"selected" => ($subpage == "recipients") ? true : false
		),
		array(
			"text" => elgg_echo("newsletter:edit:tabs:schedule"),
			"href" => $schedule_url,
			"selected" => ($subpage == "schedule") ? true : false
		)
	);
	
	echo elgg_view("navigation/tabs", array("tabs" => $tabs));