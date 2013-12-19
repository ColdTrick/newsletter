<?php
$guid = (int) get_input("guid");
$template = get_input("template");

$forward_url = REFERER;

if (!empty($guid) && !empty($template)) {
	$entity = get_entity($guid);

	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", Newsletter::SUBTYPE)) {
			
			$entity->template = "custom";
			$entity->css = elgg_view("newsletter/templates/" . $template . "/css");
			$entity->html = elgg_view("newsletter/templates/" . $template . "/body");

			system_message(elgg_echo("newsletter:action:template_to_custom:success"));

		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward($forward_url);