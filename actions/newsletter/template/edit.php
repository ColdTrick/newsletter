<?php
/**
 * Create or edit a newsletter template
 */

$guid = (int) get_input('guid');
$newsletter_guid = (int) get_input('newsletter_guid');
$html = get_input('html', '', false);
$css = strip_tags(get_input('css'));
$name = get_input('name');

if (empty($guid) && empty($newsletter_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$template = false;
$newsletter = false;

if (!empty($guid)) {
	$template = get_entity($guid);
	
	if (!$template instanceof NewsletterTemplate) {
		return elgg_error_response(elgg_echo('error:missing_data'));
	} elseif(!$template->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} elseif (!empty($newsletter_guid)) {
	$newsletter = get_entity($newsletter_guid);
	
	if (!empty($newsletter) && $newsletter->canEdit()) {
		if ($newsletter instanceof \Newsletter) {
			$template = new NewsletterTemplate();
			$template->owner_guid = $newsletter->owner_guid;
			$template->container_guid = $newsletter->container_guid;
			$template->access_id = ACCESS_PUBLIC;
			
			if ($template->save()) {
				$newsletter->template = $template->guid;
			} else {
				return elgg_error_response(elgg_echo('save:fail'));
			}
		} else {
			return elgg_error_response(elgg_echo('error:missing_data'));
		}
	} else {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
}

if (!$template instanceof NewsletterTemplate) {
	return elgg_error_response(elgg_echo('save:fail'));
}

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

if (!$template->save()) {
	return elgg_error_response(elgg_echo('newsletter:action:template:edit:error'));
}

$forward_url = REFERER;
if ($newsletter instanceof Newsletter) {
	$forward_url = elgg_generate_entity_url($newsletter, 'edit', 'template');
}

return elgg_ok_response('', elgg_echo('newsletter:action:template:edit:success'), $forward_url);
