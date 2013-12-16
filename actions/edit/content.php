<?php

$guid = get_input("guid");
$sections = get_input("sections");

if ($guid) {
	$entity = get_entity($guid);
}

if ($entity) {
	if ($entity->getSubtype() == "newsletter" && $entity->canEdit()) {
		
		// strip empty sections
		foreach ($sections as $index => $section) {
			if (empty($section["title"]) && empty($section["description"])) {
				unset($sections[$index]);
			}
		}
		
		$sections = json_encode($sections);
		$entity->sections = $sections;
	}
}

exit();