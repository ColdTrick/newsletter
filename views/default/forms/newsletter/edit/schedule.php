<?php

$entity = elgg_extract("entity", $vars);

$entity_date = gmmktime(0, 0, 0);
$entity_date += (24 * 60 * 60); // add one day for scheduling
$entity_hour = 0;
if ($entity->scheduled) {
	// input/date expects GMT timestamp
	$entity_date = gmmktime(0, 0, 0, date("n", $entity->scheduled), date("j", $entity->scheduled), date("Y", $entity->scheduled));
	$entity_hour = date("H", $entity->scheduled);
}

$date = elgg_get_sticky_value("newsletter_schedule", "date", $entity_date);
$hour = (int) elgg_get_sticky_value("newsletter_schedule", "hour", $entity_hour);
$status_notification = elgg_get_sticky_value("newsletter_schedule", "status_notification", $entity->status_notification);
if (isset($entity->show_in_archive)) {
	$show_in_archive = (int) $entity->show_in_archive;
} else {
	$show_in_archive = 1;
}
$show_in_archive = (int) elgg_get_sticky_value("newsletter_schedule", "show_in_archive", $show_in_archive);

elgg_clear_sticky_form("newsletter_schedule");

echo "<div>";
echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:schedule:description")));
echo "</div>";

echo "<div>";
echo "<label for='date'>" . elgg_echo("newsletter:schedule:date") . "</label>";
echo elgg_view("input/date", array("name" => "date", "value" => $date, "timestamp" => true, "class" => "mhm"));

echo "@";

echo "<label for='newsletter-schedule-hour' class='hidden'>" . elgg_echo("newsletter:schedule:time") . "</label>";
echo elgg_view("input/dropdown", array("name" => "hour", "value" => $hour, "options" => range(0, 23), "class" => "mlm", "id" => "newsletter-schedule-hour"));
echo ":00";
echo "</div>";

echo "<div>";
echo "<label for='newsletter-status-notification'>" . elgg_echo("newsletter:schedule:status_notification") . "</label>";
echo elgg_view("input/email", array("name" => "status_notification", "value" => $status_notification, "id" => "newsletter-status-notification"));
echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:schedule:status_notification:description") . "</div>";
echo "</div>";

$checked = array();
if ($show_in_archive) {
	$checked = array("checked" => "checked");
}
echo "<div>";
echo elgg_view("input/checkbox", array("name" => "show_in_archive", "value" => "1", "id" => "newsletter-show-in-archive") + $checked);
echo "<label for='newsletter-show-in-archive'>" . elgg_echo("newsletter:schedule:show_in_archive") . "</label>";
echo "</div>";

echo "<div class='elgg-foot'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/button", array("value" => elgg_echo("newsletter:schedule:send"), "class" => "elgg-button-action float-alt", "id" => "newsletter-send-now"));
echo elgg_view("input/submit", array("value" => elgg_echo("newsletter:schedule:save")));
echo "</div>";
