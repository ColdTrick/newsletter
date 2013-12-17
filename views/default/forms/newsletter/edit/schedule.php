<?php

	$entity = elgg_extract("entity", $vars);
	
	$entity_date = gmmktime(0, 0, 0);
	$entity_hour = 0;
	if ($entity->scheduled) {
		// input/date expects GMT timestamp
		$entity_date = gmmktime(0, 0, 0, date("n", $entity->scheduled), date("j", $entity->scheduled), date("Y", $entity->scheduled));
		$entity_hour = date("H", $entity->scheduled);
	}
	
	$date = elgg_get_sticky_value("newsletter_schedule", "date", $entity_date);
	$hour = elgg_get_sticky_value("newsletter_schedule", "hour", $entity_hour);
	$status_notification = elgg_get_sticky_value("newsletter_schedule", "status_notification", $entity->status_notification);
	
	elgg_clear_sticky_form("newsletter_schedule");
	
	echo "<div>";
	echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:schedule:description")));
	echo "</div>";
	
	echo "<div>";
	echo "<label for='date'>" . elgg_echo("newsletter:schedule:date") . "</label>";
	echo elgg_view("input/date", array("name" => "date", "value" => $date, "timestamp" => true));
	echo "</div>";
	
	echo "<div>";
	echo "<label for='newsletter-schedule-hour'>" . elgg_echo("newsletter:schedule:time") . "</label>";
	echo elgg_view("input/dropdown", array("name" => "hour", "value" => $hour, "options" => range(0, 23), "class" => "mls", "id" => "newsletter-schedule-hour"));
	echo ":00";
	echo "</div>";
	
	echo "<div>";
	echo "<label for='newsletter-status-notification'>" . elgg_echo("newsletter:schedule:status_notification") . "</label>";
	echo elgg_view("input/email", array("name" => "status_notification", "value" => $status_notification, "id" => "newsletter-status-notification"));
	echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:schedule:status_notification:description") . "</div>";
	echo "</div>";
	
	echo "<div class='elgg-foot'>";
	echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
	echo elgg_view("input/submit", array("value" => elgg_echo("save")));
	echo "</div>";