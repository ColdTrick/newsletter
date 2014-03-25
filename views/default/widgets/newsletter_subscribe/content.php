<?php
/**
 * Show the subscribe form in a widget
 */

$widget = elgg_extract("entity", $vars);
$owner = $widget->getOwnerEntity();

echo elgg_view_form("newsletter/subscribe", array(), array("entity" => $owner));
