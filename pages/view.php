<?php

/**
 * View a newsletter online
 *
 * @uses get_input("guid") the guid of the newsletter
 */

$guid = (int) get_input("guid");

// validate input
if (empty($guid)) {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	forward(REFERER);
}


// correct check if entity can be found or if there is a valid secret to ignore access
$entity = get_entity($guid);

echo elgg_view_layout("newsletter", array("entity" => $entity));
echo elgg_view("newsletter/buttons", array("entity" => $entity, "type" => "view"));