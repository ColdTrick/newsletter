<?php
/**
 * Create or edit a newsletter template
 */
$guid = (int) get_input("guid");
$newsletter_guid = (int) get_input("newsletter_guid");
$html = get_input("html", "", false);
$css = strip_tags(get_input("css"));
$name = get_input("name");

$forward_url = REFERER;

if (!empty($guid) || !empty($newsletter_guid)) {
	if (!empty($guid)) {
		$template = get_entity($guid);
		
		if (!empty($template) && $template->canEdit()) {
			if (!elgg_instanceof($template, "object", NEWSLETTER_TEMPLATE)) {
				unset($template);
				register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . NEWSLETTER_TEMPLATE))));
			}
		} else {
			unset($template);
			register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
		}
	} elseif (!empty($newsletter_guid)) {
		$newsletter = get_entity($newsletter_guid);
		
		if (!empty($newsletter) && $newsletter->canEdit()) {
			if (elgg_instanceof($newsletter, "object", Newsletter::SUBTYPE)) {
				$template = new ElggObject();
				$template->subtype = NEWSLETTER_TEMPLATE;
				$template->owner_guid = $newsletter->owner_guid;
				$template->container_guid = $newsletter->container_guid;
				$template->access_id = ACCESS_PUBLIC;
				
				if ($template->save()) {
					$newsletter->template = $template->getGUID();
				} else {
					unset($template);
					register_error(elgg_echo("IOException:UnableToSaveNew", array(elgg_echo("item:object:" . NEWSLETTER_TEMPLATE))));
				}
			} else {
				register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:" . Newsletter::SUBTYPE))));
			}
		} else {
			register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
		}
	}
	
	if (!empty($template)) {
		$template->title = $name;
		
		if (!empty($html)) {
			$template->html = $html;
		} else {
			unset($template->html);
		}
		
		if (!empty($css)) {
			$template->css = $css;
		} else {
			unset($template->css);
		}
		
		if ($template->save()) {
			// @todo better forwarding urls
			if (!empty($newsletter)) {
				$forward_url = "newsletter/edit/" . $newsletter->getGUID() . "/template";
			}
			system_message(elgg_echo("newsletter:action:template:edit:success"));
		} else {
			register_error(elgg_echo("newsletter:action:template:edit:error"));
		}
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward($forward_url);
