<?php

$entity = elgg_extract("entity", $vars);
$user = elgg_get_logged_in_user_entity();
if (empty($user) || empty($entity)) {
	return true;
}
echo elgg_view("input/email", array("name" => "email", "value" => $user->email, "placeholder" => elgg_echo("newsletter:recipients:email")));
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/submit", array("value" => elgg_echo("send")));