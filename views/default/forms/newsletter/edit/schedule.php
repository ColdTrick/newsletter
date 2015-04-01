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
echo elgg_view("input/select", array("name" => "hour", "value" => $hour, "options" => range(0, 23), "class" => "mlm", "id" => "newsletter-schedule-hour"));
echo ":00";
echo "</div>";

$status_class = "newsletter-status-notification";
if (empty($status_notification)) {
	echo "<div class='" . $status_class . "'>";
	echo elgg_view("input/checkbox", array(
		"name" => "status_notification_me",
		"value" => elgg_get_logged_in_user_entity()->email,
		"id" => "newsletter-status-notification-me",
		"default" => false
	));
	echo "<label for='newsletter-status-notification-me'>" . elgg_echo("newsletter:schedule:status_notification:me") . "</label>";
	echo elgg_view("output/url", array(
		"text" => elgg_echo("newsletter:schedule:status_notification:toggle"),
		"href" => "#",
		"id" => "newsletter-status-notification-toggle",
		"class" => "mls"
	));
	echo "</div>";
	
	$status_class .= " hidden";
}

echo "<div class='" . $status_class . "'>";
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

$save_and_send_now_options = array(
	"value" => elgg_echo("newsletter:schedule:send"),
	"class" => "elgg-button-action float-alt",
	"id" => "newsletter-send-now"
);
$save_and_schedule_options = array(
	"value" => elgg_echo("newsletter:schedule:save"),
	"class" => "elgg-button-submit"
);

if (!$entity->getRecipients()) {
	$save_and_send_now_options["class"] .= " elgg-requires-confirmation";
	$save_and_send_now_options["data-confirm"] = elgg_echo("newsletter:schedule:no_recipients");
	
	$save_and_schedule_options["class"] .= " elgg-requires-confirmation";
	$save_and_schedule_options["data-confirm"] = elgg_echo("newsletter:schedule:no_recipients");
}

echo elgg_view("input/button", $save_and_send_now_options);
echo elgg_view("input/submit", $save_and_schedule_options);
echo "</div>";
