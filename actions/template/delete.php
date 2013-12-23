<?php
$guid = (int) get_input("guid");

$forward_url = REFERER;

if (!empty($guid)) {
	$entity = get_entity($guid);

	if (!empty($entity) && $entity->canEdit()) {
		if (elgg_instanceof($entity, "object", NEWSLETTER_TEMPLATE)) {
			$entity_url = $entity->getURL();
			$container = $entity->getContainerEntity();

			if ($entity->delete()) {
				// check if we don't forward to the entity url
				if ($_SERVER["HTTP_REFERER"] == $entity_url) {
					if (elgg_instanceof($container, "group")) {
						$forward_url = "newsletter/group/" . $container->getGUID();
					} else {
						$forward_url = "newsletter/site";
					}
				}
				
				system_message(elgg_echo("newsletter:action:template:delete:success"));
			} else {
				register_error(elgg_echo("newsletter:action:template:delete:error:delete"));
			}
		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . NEWSLETTER_TEMPLATE))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward($forward_url);