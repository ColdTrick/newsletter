<?php
/**
 * Newsletter templates in version <= 1.0 were saved with wrong access.
 * This needs to be restored
 */

$options = array(
	"type" => "object",
	"subtype" => NEWSLETTER_TEMPLATE,
	"limit" => false,
	"site_guids" => false,
	"wheres" => array("e.access_id <> " . ACCESS_PUBLIC)
);

// ignore access during this run
$ia = elgg_set_ignore_access(true);

// fetch all 'wrong' templates
$batch = new ElggBatch("elgg_get_entities", $options, null, 25, false);

foreach ($batch as $template) {
	$template->access_id = ACCESS_PUBLIC;
	$template->save();
}

// restore access
elgg_set_ignore_access($ia);
