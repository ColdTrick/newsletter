<?php

$entity = elgg_extract("entity", $vars);
$site = elgg_get_site_entity();

$title = elgg_echo("newsletter:subscriptions:site:title");

$content = elgg_view("output/longtext", array("value" => elgg_echo("newsletter:subscriptions:site:description"), "class" => "mtn mbs"));

$content .= "<table class='elgg-table-alt'>";
$content .= "<tr>";
$content .= "<th>&nbsp;</th>";
$content .= "<th class='newsletter-settings-small'>" . elgg_echo("on") . "</th>";
$content .= "<th class='newsletter-settings-small'>" . elgg_echo("off") . "</th>";
$content .= "</tr>";

$on = "";
$off = "checked='checked'";
if (newsletter_check_user_subscription($entity, $site)) {
	$on = "checked='checked'";
	$off = "";
}

$content .= "<tr>";
$content .= "<td>" . $site->name . "</td>";
$content .= "<td class='newsletter-settings-small'><input type='radio' name='subscriptions[" . $site->getGUID() . "]' value='1' " . $on . " /></td>";
$content .= "<td class='newsletter-settings-small'><input type='radio' name='subscriptions[" . $site->getGUID() . "]' value='0' " . $off . " /></td>";
$content .= "</tr>";
$content .= "</table>";

$content .= "<div class='mtm'>";
$checked = array();
if (check_entity_relationship($entity->getGUID(), NewsletterSubscription::GENERAL_BLACKLIST, $site->getGUID())) {
	$checked = array("checked" => "checked");
}
$content .= elgg_view("input/checkbox", array("name" => "block_all", "value" => "1", "id" => "newsletter-subscription-block-all") + $checked);
$content .= "<label for='newsletter-subscription-block-all'>" . elgg_echo("newsletter:unsubscribe:all", array($site->name)) . "</label>";
$content .= "</div>";

echo elgg_view_module("info", $title, $content);