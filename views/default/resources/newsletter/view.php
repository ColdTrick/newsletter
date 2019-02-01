<?php
/**
 * View a newsletter online
 *
 * @uses get_input('guid') the guid of the newsletter
 */

use Elgg\BadRequestException;
use Elgg\EntityNotFoundException;
use Elgg\ValidationException;
use Elgg\EntityPermissionsException;

$guid = (int) get_input('guid');
$code = get_input('code');

// validate input
if (empty($guid)) {
	throw new BadRequestException();
}

if (!elgg_entity_exists($guid)) {
	throw new EntityNotFoundException();
}

// validate code
if (empty($code) || !newsletter_validate_commandline_secret($guid, $code)) {
	throw new ValidationException(elgg_echo('newsletter:entity:error:code'));
}

// validate entity
$entity = get_entity($guid);
if (!$entity instanceof Newsletter) {
	throw new EntityPermissionsException();
}

// view the newsletter
echo elgg_view_layout('newsletter', [
	'entity' => $entity,
]);

// add some helper buttons
echo elgg_view('newsletter/buttons', [
	'entity' => $entity,
	'type' => 'view',
]);
