<?php

$entity = $vars["entity"];

$template_options = array(
	"default" => elgg_echo("newsletter:edit:template:select:default")
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
		$template_options[$template->getGUID()] = $template->title;
	}
}

$template_options["custom"] = elgg_echo("newsletter:edit:template:select:custom");

$template = "default";
if ($entity->template) {
	$template = $entity->template;
}

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:template:description")));

echo "<div class='mtm'><label for='newsletter-edit-template-select'>" . elgg_echo("newsletter:edit:template:select") . "</label><br />";
echo "<ul id='newsletter-edit-template-select' class='elgg-input-radios elgg-vertical'>";

$confirm_options = array(
		"confirm" => elgg_echo("newsletter:edit:template:copy_to_custom:confirm"),
		"text" => elgg_echo("newsletter:edit:template:copy_to_custom"),
		"class" => "mlm hidden"
	);

foreach ($template_options as $name => $label) {
	$checked = "";
	if ($name == $template) {
		$checked = " checked='checked'";
	}
	
	echo "<li>";
	echo "<label>";
	echo "<input id='newsletter-edit-template-select' class='elgg-input-radio' type='radio' value='$name'$checked name='template' />";
	echo $label;
	echo "</label>";
	
	if ($name !== "custom") {
		$confirm_options["href"] = "action/newsletter/edit/template_to_custom?guid=" . $entity->guid . "&template=" . $name;
		echo elgg_view("output/confirmlink", $confirm_options);
	}
	
	echo "</li>";
}
echo "</ul>";

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
