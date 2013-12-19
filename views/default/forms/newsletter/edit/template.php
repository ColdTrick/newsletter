<?php

$entity = $vars["entity"];

$template_options = array(
	elgg_echo("newsletter:edit:template:select:default") => "default"
);

$options = array(
	"type" => "object",
	"subtype" => NEWSLETTER_TEMPLATE,
	"container_guid" => $entity->getContainerGUID(),
	"limit" => false
);
$templates = elgg_get_entities($options);
if (!empty($templates)) {
	foreach ($templates as $template) {
		$template_options[$template->title] = $template->getGUID();
	}
}

$template_options[elgg_echo("newsletter:edit:template:select:custom")] = "custom";

$template = "default";
if ($entity->template) {
	$template = $entity->template;
}

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:template:description")));

echo "<div class='mtm'><label for='newsletter-edit-template-select'>" . elgg_echo("newsletter:edit:template:select") . "</label><br />";
echo elgg_view("input/radio", array("id" => "newsletter-edit-template-select", "name" => "template", "options" => $template_options, "value" => $template));
echo "</div>";

$class = "hidden";
if ($entity->template == "custom") {
	$class = "";
}

echo "<div class='newsletter-edit-template-custom " . $class . "'>";

echo "<div><label>" . elgg_echo("newsletter:edit:template:html") . "<br />";
echo elgg_view("input/plaintext", array("name" => "html", "value" => $entity->html));
echo "</label></div>";

echo "<div><label>" . elgg_echo("newsletter:edit:template:css") . "<br />";
echo elgg_view("input/plaintext", array("name" => "css", "value" => $entity->css));
echo "</label></div>";

echo "<div><label>" . elgg_echo("newsletter:edit:template:name") . "<br />";
echo elgg_view("input/text", array("name" => "name"));
echo "</label></div>";

echo "</div>";

echo "<div class='elgg-foot'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/button", array("value" => elgg_echo("newsletter:edit:template:save_as"), "id" => "newsletter-edit-template-save-as", "class" => "elgg-button-action float-alt " . $class));
echo elgg_view("input/submit", array("value" => elgg_echo("save")));
echo "</div>";
