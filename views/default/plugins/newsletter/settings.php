<?php

$plugin = elgg_extract("entity", $vars);

$noyes_options = array(
	"no" => elgg_echo("option:no"),
	"yes" => elgg_echo("option:yes")
);

$url_postfix_replacements = array(
	'{guid}'
);

echo "<div>";
echo elgg_echo("newsletter:settings:allow_groups");
echo elgg_view("input/dropdown", array(
	"name" => "params[allow_groups]",
	"value" => $plugin->allow_groups,
	"options_values" => $noyes_options,
	"class" => "mls"
));
echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:settings:allow_groups:description") . "</div>";
echo "</div>";

echo "<div>";
echo elgg_echo("newsletter:settings:include_existing_users");
echo elgg_view("input/dropdown", array(
	"name" => "params[include_existing_users]",
	"value" => $plugin->include_existing_users,
	"options_values" => array_reverse($noyes_options),
	"class" => "mls"
));
echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:settings:include_existing_users:description") . "</div>";
echo "</div>";

echo "<div>";
echo elgg_echo("newsletter:settings:custom_from");
echo elgg_view("input/dropdown", array(
	"name" => "params[custom_from]",
	"value" => $plugin->custom_from,
	"options_values" => $noyes_options,
	"class" => "mls"
));
echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:settings:custom_from:description") . "</div>";
echo "</div>";

echo "<div>";
echo elgg_echo("newsletter:settings:custom_from:domains");
echo elgg_view("input/text", array(
	"name" => "params[custom_from_domains]",
	"value" => $plugin->custom_from_domains,
));
echo "<div class='elgg-subtext'>" . elgg_echo("newsletter:settings:custom_from:domains:description") . "</div>";
echo "</div>";

// url postfix
$url_postfix = elgg_view("output/longtext", array("value" => elgg_echo("newsletter:settings:url_postfix:description")));

if ($plugin->url_postfix_name || $plugin->url_postfix_value) {
	$url_postfix .= '<div class="elgg-message elgg-state-error">' . elgg_echo("newsletter:settings:url_postfix:upgrade") . '</div>';
}

$url_postfix .= "<div>";
$url_postfix .= elgg_echo("newsletter:settings:url_postfix:setting");
$url_postfix .= elgg_view("input/plaintext", array(
	"name" => "params[url_postfix]",
	"value" => $plugin->url_postfix
));
$url_postfix .= '<div class="elgg-subtext">' . elgg_echo("newsletter:settings:url_postfix:setting:description", array(implode(' ', $url_postfix_replacements))) . '</div>';
$url_postfix .= "</div>";

echo elgg_view_module("inline", elgg_echo("newsletter:settings:url_postfix"), $url_postfix);
