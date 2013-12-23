<?php
/**
 * View a newsletter online
 *
 * @uses get_input("guid") the guid of the newsletter
 */

$guid = (int) get_input("guid");
$code = get_input("code");

// validate input
if (empty($guid)) {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	forward(REFERER);
}

$ia = elgg_get_ignore_access();
$entity = get_entity($guid);
if (empty($entity)) {
	// does the entity exist
	if (!elgg_entity_exists($guid)) {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
		forward(REFERER);
	}
	
	// validate code
	if (empty($code) || !newsletter_validate_commandline_secret($guid, $code)) {
		register_error(elgg_echo("newsletter:entity:error:code"));
		forward(REFERER);
	}
	
	// code is valid, so get the entity
	$ia = elgg_set_ignore_access(true);
	$entity = get_entity($guid);
}

// validate entity
if (empty($entity) || !elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
	register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
	
	// restore access
	elgg_set_ignore_access($ia);
	
	forward(REFERER);
}

// view the newsletter
echo elgg_view_layout("newsletter", array("entity" => $entity));

// restore access
elgg_set_ignore_access($ia);

// add some helper buttons
echo elgg_view("newsletter/buttons", array("entity" => $entity, "type" => "view"));
