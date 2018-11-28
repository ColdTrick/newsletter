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
				$newsletter->template = $template->getGUID();
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

$forward_url = REFERER;
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
			$forward_url = 'newsletter/edit/' . $newsletter->getGUID() . '/template';
		}
		
		return elgg_ok_response('', elgg_echo('newsletter:action:template:edit:success'), $forward_url);
	} else {
		return elgg_error_response(elgg_echo('newsletter:action:template:edit:error'));
	}
}
