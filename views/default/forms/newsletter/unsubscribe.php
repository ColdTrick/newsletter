<?php

/**
 * Form content for the unsubscribe action
 * Users get here by a link in their emailed newsletter
 *
 * @uses	$vars['entity']		The container to unsubscribe from (primairly)
 * @uses	$vars['recipient']	The recipient of the newsletter (user_guid or email address)
 * @uses	$vars['code']		The validation code, to be used in the action
 */

$entity = elgg_extract("entity", $vars);
$recipient = elgg_extract("recipient", $vars);

if (is_numeric($recipient)) {
	$user = get_user($recipient);
	
	echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:unsubscribe:user", array($user->name))));
} elseif (newsletter_is_email_address($recipient)) {
	echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:unsubscribe:email", array($recipient))));
} elseif (!$recipient) {
	echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:unsubscribe:email:empty")));
	echo elgg_view("input/text", array("name" => "recipient")) . "<br /><br />";
}

echo "<div>";
// unsubscribe from the provided container
echo elgg_view("input/checkbox", array("name" => "guid", "value" => $entity->getGUID(), "checked" => "checked", "id" => "newsletter-unsubscribe-entity"));
echo "<label for='newsletter-unsubscribe-entity'>" . elgg_echo("newsletter:unsubscribe:entity", array($entity->name)) . "</label>";
echo "<br />";
// unsubscribe from all newsletters
echo elgg_view("input/checkbox", array("name" => "all", "value" => "1", "id" => "newsletter-unsubscribe-all"));
echo "<label for='newsletter-unsubscribe-all'>" . elgg_echo("newsletter:unsubscribe:all", array(elgg_get_site_entity()->name)) . "</label>";

echo "</div>";

echo "<div class='elgg-foot'>";

if ($recipient) {
	echo elgg_view("input/hidden", array("name" => "recipient", "value" => $recipient));
}

echo elgg_view("input/hidden", array("name" => "code", "value" => elgg_extract("code", $vars)));
echo elgg_view("input/hidden", array("name" => "entity_guid", "value" => $entity->getGUID()));
echo elgg_view("input/submit", array("value" => elgg_echo("newsletter:unsubscribe")));
echo "</div>";