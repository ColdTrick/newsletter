<?php

$entity = elgg_extract("entity", $vars);
$container = $entity->getContainerEntity();

$recipients = $entity->getRecipients();
if (!empty($recipients)) {
	
	$user_guids = elgg_get_sticky_value("newsletter_recipients", "user_guids", elgg_extract("user_guids", $recipients));
	$group_guids = elgg_get_sticky_value("newsletter_recipients", "group_guids", elgg_extract("group_guids", $recipients));
	$emails = elgg_get_sticky_value("newsletter_recipients", "emails", elgg_extract("emails", $recipients));
	
	$subscribers = (int) elgg_get_sticky_value("newsletter_recipients", "subscribers", elgg_extract("subscribers", $recipients));
	$members = (int) elgg_get_sticky_value("newsletter_recipients", "members", elgg_extract("members", $recipients));
} else {
	$user_guids = elgg_get_sticky_value("newsletter_recipients", "user_guids");
	$group_guids = elgg_get_sticky_value("newsletter_recipients", "group_guids");
	$emails = elgg_get_sticky_value("newsletter_recipients", "emails");
	
	$subscribers = (int) elgg_get_sticky_value("newsletter_recipients", "subscribers");
	$members = (int) elgg_get_sticky_value("newsletter_recipients", "members");
}

elgg_load_js('jquery.ui.autocomplete.html');
elgg_require_js('newsletter/recipients');

echo "<div>";
echo elgg_view("output/longtext", array("value" => elgg_echo("newsletter:recipients:description")));
echo "</div>";

echo "<div>";
echo "<label for='newsletter-recipients-csv'>" . elgg_echo("newsletter:recipients:csv") . "</label>";
echo elgg_view("input/file", array("name" => "csv", "id" => "newsletter-recipients-csv"));
echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:recipients:csv:description") . "</div>";
echo "</div>";

echo "<div>";
echo "<label for='newsletter-recipients-autocomplete'>" . elgg_echo("newsletter:recipients:recipient") . "</label>";
echo elgg_view("input/text", array("name" => "q", "id" => "newsletter-recipients-autocomplete", "class" => "elgg-input-autocomplete"));
echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:recipients:recipient:description") . "</div>";
echo "</div>";

// recipient wrapper
// add subscribers
$checked = array();
if (!empty($subscribers)) {
	$checked = array("checked" => "checked");
}
$subscriber_count = newsletter_get_subscribers($container, true);

$checkboxes = "<div>";
$checkboxes .= elgg_view("input/checkbox", array("name" => "subscribers", "value" => "1", "id" => "newsletter-recipients-subscribers") + $checked);
$checkboxes .= "<label for='newsletter-recipients-subscribers'>" . elgg_echo("newsletter:recipients:subscribers") . "<span class='mls'>(" . $subscriber_count . ")</span></label>";
$checkboxes .= "</div>";

// add members
$checked = array();
if (!empty($members)) {
	$checked = array("checked" => "checked");
}

$member_count = 0;
if (elgg_instanceof($container, "site")) {
	$options = array(
		"site_guids" => false,
		"count" => true,
		"type" => "user"
	);
	$member_count = $container->getEntities($options);
	
	$member_count .= " " . elgg_echo("newsletter:recipients:members:site");
} elseif (elgg_instanceof($container, "group")) {
	$member_count = $container->getMembers(0, 0, true);
	
	$member_count .= " " . elgg_echo("newsletter:recipients:members:group");
}

$checkboxes .= "<div>";
$checkboxes .= elgg_view("input/checkbox", array("name" => "members", "value" => "1", "id" => "newsletter-recipients-members") + $checked);
$checkboxes .= "<label for='newsletter-recipients-members'>" . elgg_echo("newsletter:recipients:members") . "<span class='mls'>(" . $member_count . ")</span></label>";
$checkboxes .= "</div>";

// add specific users
$users_content = "";
$class = "hidden";
$counter = "";

if (!empty($user_guids)) {
	$counter = 0;
	
	foreach ($user_guids as $user_guid) {
		$user = get_user($user_guid);
		
		if (!empty($user)) {
			$class = "";
			$counter++;
			
			$user_listing = newsletter_format_recipient($user);
			
			$users_content .= elgg_extract("content", $user_listing);
		}
	}
	
	if (!empty($counter)) {
		$counter = "(" . $counter . ")";
	} else {
		$counter = "";
	}
}

$users = elgg_view_module("newsletter-recipients", elgg_echo("item:user") . "<span class='newsletter-counter mls'>" . $counter . "</span>", $users_content, array("class" => $class, "id" => "newsletter-recipients-users"));

// add specific groups
$groups_content = "";
$class = "hidden";
if (!empty($group_guids)) {
	$counter = 0;
	
	foreach ($group_guids as $group_guid) {
		$group = get_entity($group_guid);
			
		if (!empty($group) && elgg_instanceof($group, "group")) {
			$class = "";
			$counter++;
			
			$group_listing = newsletter_format_recipient($group);

			$groups_content .= elgg_extract("content", $group_listing);
		}
	}
	
	if (!empty($counter)) {
		$counter = "(" . $counter . ")";
	} else {
		$counter = "";
	}
}

$groups = elgg_view_module("newsletter-recipients", elgg_echo("groups") . "<span class='newsletter-counter mls'>" . $counter . "</span>", $groups_content, array("class" => $class, "id" => "newsletter-recipients-groups"));

// add specific emails
$email_content = "";
$class = "hidden";
if (!empty($emails)) {
	$counter = 0;
	
	foreach ($emails as $email) {
		$email_listing = newsletter_format_recipient($email);

		if (!empty($email_listing)) {
			$class = "";
			$counter++;
			
			$email_content .= elgg_extract("content", $email_listing);
		}
	}
	
	if (!empty($counter)) {
		$counter = "(" . $counter . ")";
	} else {
		$counter = "";
	}
}

$emails = elgg_view_module("newsletter-recipients", elgg_echo("newsletter:recipients:email") . "<span class='newsletter-counter mls'>" . $counter . "</span>", $email_content, array("class" => $class, "id" => "newsletter-recipients-emails"));

$icon_options = array(
	"class" => array("elgg-icon-info", "mlm"),
	"title" => elgg_echo("newsletter:recipients:tooltip")
);

$wrapper_title = elgg_echo("newsletter:recipients") . elgg_view("output/icon", $icon_options);

echo elgg_view_module("newsletter-recipients-wrapper", $wrapper_title, $checkboxes . $users . $groups . $emails, array("id" => "newsletter-recipients-wrapper"));

echo "<div class='elgg-foot'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/submit", array("value" => elgg_echo("save")));
echo "</div>";
