<?php
/**
 * To unsubscribe from the newsletter of a container (and all others)
 *
 * @uses elgg_get_page_onwer_entity() from which container to unsubscribe primairly
 */

use Elgg\BadRequestException;
use Elgg\EntityNotFoundException;
use Elgg\ValidationException;

$guid = (int) elgg_extract('guid', $vars, get_input('guid'));
$code = get_input('c');
$user_guid = (int) get_input('u');
$email = get_input('e');

// check if we have correct input
if (empty($guid)) {
	throw new BadRequestException(elgg_echo('newsletter:unsubscribe:error:input'));
}

$entity = get_entity($guid);
if (!$entity instanceof ElggSite && !$entity instanceof ElggGroup) {
	throw new EntityNotFoundException();
}

if (!empty($user_guid)) {
	if (elgg_is_logged_in() && ($user_guid !== elgg_get_logged_in_user_guid())) {
		// got the link from a forwarded email?
		$forward_url = elgg_generate_url('collection:object:newsletter:site');
		if ($entity instanceof ElggGroup) {
			$forward_url = elgg_generate_url('collection:object:newsletter:group', [
				'guid' => $entity->guid,
			]);
		}
		$e = new Elgg\ValidationException(elgg_echo('newsletter:unsubscribe:error:invalid_user'));
		$e->setRedirectUrl($forward_url);
		throw $e;
	}
	
	$recipient = $user_guid;
} else {
	$recipient = $email;
}

// validate validation code
if ($code && !newsletter_validate_unsubscribe_code($entity, $recipient, $code)) {
	throw new ValidationException(elgg_echo('newsletter:unsubscribe:error:code'));
}
	
// breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $entity instanceof ElggGroup ? $entity : null);

// build page elements
$title_text = elgg_echo('newsletter:unsubscribe:title');

$form = elgg_view_form('newsletter/unsubscribe', [], [
	'entity' => $entity,
	'recipient' => $recipient,
	'code' => $code,
]);

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $form,
]);

// draw page
echo elgg_view_page($title_text, $page_data);
