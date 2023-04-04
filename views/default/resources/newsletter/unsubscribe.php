<?php
/**
 * To unsubscribe from the newsletter of a container (and all others)
 *
 * @uses elgg_get_page_onwer_entity() from which container to unsubscribe primairly
 */

use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Exceptions\Http\EntityNotFoundException;
use Elgg\Exceptions\Http\ValidationException;

$guid = (int) elgg_extract('guid', $vars, get_input('guid'));
$code = get_input('c');
$user_guid = (int) get_input('u');
$email = get_input('e');

if (empty($guid)) {
	throw new BadRequestException(elgg_echo('newsletter:unsubscribe:error:input'));
}

$entity = get_entity($guid);
if (!$entity instanceof \ElggSite && !$entity instanceof \ElggGroup) {
	throw new EntityNotFoundException();
}

if (!empty($user_guid)) {
	if (elgg_is_logged_in() && ($user_guid !== elgg_get_logged_in_user_guid())) {
		// got the link from a forwarded email?
		$forward_url = elgg_generate_url('collection:object:newsletter:site');
		if ($entity instanceof \ElggGroup) {
			$forward_url = elgg_generate_url('collection:object:newsletter:group', [
				'guid' => $entity->guid,
			]);
		}
		
		$e = new ValidationException(elgg_echo('newsletter:unsubscribe:error:invalid_user'));
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

elgg_push_collection_breadcrumbs('object', \Newsletter::SUBTYPE, $entity instanceof \ElggGroup ? $entity : null);

echo elgg_view_page(elgg_echo('newsletter:unsubscribe:title'), [
	'content' => elgg_view_form('newsletter/unsubscribe', [], [
		'entity' => $entity,
		'recipient' => $recipient,
		'code' => $code,
	]),
	'filter' => false,
]);
