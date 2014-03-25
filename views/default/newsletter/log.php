<?php

/**
 * Show the logging of the newsletter
 *
 * @uses $vars['entity']	The newsletter the view
 */

$entity = elgg_extract("entity", $vars);

$log = $entity->getLogging();

if (!empty($log)) {
	// general info
	$title = elgg_echo("newsletter:log:general:title");
	
	$general = "<table class='elgg-table-alt'>";
	$general .= "<tr>";
	$general .= "<td>" . elgg_echo("newsletter:log:general:scheduled") . "</td>";
	$general .= "<td>" . date(elgg_echo("friendlytime:date_format"), $entity->scheduled) . "</td>";
	$general .= "</tr>";
	$general .= "<tr>";
	$general .= "<td>" . elgg_echo("newsletter:log:general:starttime") . "</td>";
	$general .= "<td>" . date(elgg_echo("friendlytime:date_format"), elgg_extract("start_time", $log)) . "</td>";
	$general .= "</tr>";
	$general .= "<tr>";
	$general .= "<td>" . elgg_echo("newsletter:log:general:endtime") . "</td>";
	$general .= "<td>";
	if ($entity->status == "sent") {
		$general .= date(elgg_echo("friendlytime:date_format"), elgg_extract("end_time", $log));
	} else {
		$general .= elgg_echo("newsletter:status:" . $entity->status);
	}
	$general .= "</td>";
	$general .= "</tr>";
	$general .= "</table>";
	
	echo elgg_view_module("info", $title, $general);
	
	// recipient logging
	$recipients = elgg_extract("recipients", $log);
	
	if (!empty($recipients)) {
		$users_title = elgg_echo("newsletter:log:users:title");
		$emails_title = elgg_echo("newsletter:log:emails:title");
		
		$users_header = "";
		$users_content = "";
		$users_row_class = "";
		$users_counter = 0;
		$users_error_counter = 0;
		$emails_header = "";
		$emails_content = "";
		$emails_row_class = "";
		$emails_counter = 0;
		$emails_error_counter = 0;
		
		foreach ($recipients as $recipient) {
			$type = elgg_extract("type", $recipient);
			
			if ($type == "users") {
				$users_counter++;
				if (($users_counter > 25) && empty($users_row_class)) {
					$users_row_class = "hidden";
				}
				$skip_columns = array("type", "timestamp");
				if (!elgg_is_admin_logged_in()) {
					$skip_columns[] = "email";
				}
				
				if (empty($users_header)) {
					$keys = array_keys($recipient);
					
					$users_header = "<tr>";
					foreach ($keys as $key) {
						if (!in_array($key, $skip_columns)) {
							if ($key == "status") {
								$users_header .= "<th class='center'>" . elgg_echo("newsletter:log:users:header:" . $key) . "</th>";
							} else {
								$users_header .= "<th>" . elgg_echo("newsletter:log:users:header:" . $key) . "</th>";
							}
						}
					}
					$users_header .= "</tr>";
				}
				
				$users_content .= "<tr class='" . $users_row_class . "'>";
				foreach ($recipient as $key => $data) {
					if (!in_array($key, $skip_columns)) {
						switch ($key) {
							case "guid":
								$user = get_user($data);
								$users_content .= "<td>" . $user->name . "</td>";
								break;
							case "status":
								if ($data) {
									$users_content .= "<td class='center'>" . elgg_view_icon("checkmark") . "</td>";
								} else {
									$users_error_counter++;
									$users_content .= "<td class='center'>" . elgg_view_icon("attention") . "</td>";
								}
								break;
							default:
								$users_content .= "<td>" . $data . "</td>";
								break;
						}
						
					}
				}
				$users_content .= "</tr>";
			} elseif ($type == "emails") {
				$emails_counter++;
				if (($emails_counter > 25) && empty($emails_row_class)) {
					$emails_row_class = "hidden";
				}
				$skip_columns = array("type", "timestamp");
				
				if (empty($emails_header)) {
					$keys = array_keys($recipient);
						
					$emails_header = "<tr>";
					foreach ($keys as $key) {
						if (!in_array($key, $skip_columns)) {
							if ($key == "status") {
								$emails_header .= "<th class='center'>" . elgg_echo("newsletter:log:email:header:" . $key) . "</th>";
							} else {
								$emails_header .= "<th>" . elgg_echo("newsletter:log:email:header:" . $key) . "</th>";
							}
						}
					}
					$emails_header .= "</tr>";
				}
				
				$emails_content .= "<tr class='" . $emails_row_class . "'>";
				foreach ($recipient as $key => $data) {
					if (!in_array($key, $skip_columns)) {
						switch ($key) {
							case "status":
								if ($data) {
									$emails_content .= "<td class='center'>" . elgg_view_icon("checkmark") . "</td>";
								} else {
									$emails_error_counter++;
									$emails_content .= "<td class='center'>" . elgg_view_icon("attention") . "</td>";
								}
								break;
							default:
								$emails_content .= "<td>" . $data . "</td>";
								break;
						}
				
					}
				}
				$emails_content .= "</tr>";
			}
		}
		
		if (!empty($users_content)) {
			$users_title .= " (";
			$users_title .= ($users_counter - $users_error_counter) . " " . elgg_echo("newsletter:log:counter:success");
			$users_title .= ", " . $users_error_counter . " " . elgg_echo("newsletter:log:counter:error");
			$users_title .= ")";
			
			$users_content = "<table class='elgg-table' id='newsletter-log-users-table'>" . $users_header . $users_content . "</table>";
			
			if (!empty($users_row_class)) {
				$users_content .= "<div class='mtm'>";
				$users_content .= elgg_view("output/url", array(
					"text" => elgg_echo("more"),
					"href" => "#newsletter-log-users-table",
					"onclick" => "$('#newsletter-log-users-table tr.hidden').show();$(this).parent().remove();",
					"class" => "float-alt"
				));
				$users_content .= "</div>";
			}
		} else {
			$users_content = elgg_view("output/longtext", array("value" => elgg_echo("newsletter:log:users:no_recipients")));
		}
		
		if (!empty($emails_content)) {
			$emails_title .= " (";
			$emails_title .= ($emails_counter - $emails_error_counter) . " " . elgg_echo("newsletter:log:counter:success");
			$emails_title .= ", " . $emails_error_counter . " " . elgg_echo("newsletter:log:counter:error");
			$emails_title .= ")";
			
			$emails_content = "<table class='elgg-table' id='newsletter-log-emails-table'>" . $emails_header . $emails_content . "</table>";
			
			if (!empty($emails_row_class)) {
				$emails_content .= "<div class='mtm'>";
				$emails_content .= elgg_view("output/url", array(
					"text" => elgg_echo("more"),
					"href" => "#newsletter-log-emails-table",
					"onclick" => "$('#newsletter-log-emails-table tr.hidden').show();$(this).parent().remove();",
					"class" => "float-alt"
				));
				$emails_content .= "</div>";
			}
		} else {
			$emails_content = elgg_view("output/longtext", array("value" => elgg_echo("newsletter:log:emails:no_recipients")));
		}
		
		echo elgg_view_module("info", $users_title, $users_content);
		echo elgg_view_module("info", $emails_title, $emails_content);
	} else {
		echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:log:no_recipients")));
	}
	
} else {
	echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:log:no_contents")));
}
