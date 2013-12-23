<?php

	$plugin = elgg_extract("entity", $vars);
	
	$noyes_options = array(
		"no" => elgg_echo("option:no"),
		"yes" => elgg_echo("option:yes")
	);
	
	echo "<div>";
	echo elgg_echo("newsletter:settings:allow_groups");
	echo elgg_view("input/dropdown", array("name" => "params[allow_groups]", "value" => $plugin->allow_groups, "options_values" => $noyes_options, "class" => "mls"));
	echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:settings:allow_groups:description") . "</div>";
	echo "</div>";
	
	echo "<div>";
	echo elgg_echo("newsletter:settings:include_existing_users");
	echo elgg_view("input/dropdown", array("name" => "params[include_existing_users]", "value" => $plugin->include_existing_users, "options_values" => array_reverse($noyes_options), "class" => "mls"));
	echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:settings:include_existing_users:description") . "</div>";
	echo "</div>";
	
	