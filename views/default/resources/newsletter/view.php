<?php
/**
 * View a newsletter online
 *
 * @uses get_input('guid') the guid of the newsletter
 */

use Elgg\ValidationException;

$guid = (int) get_input('guid');
$code = get_input('code');

// validate input
elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);

// validate code
if (empty($code) || !newsletter_validate_commandline_secret($guid, $code)) {
	throw new ValidationException(elgg_echo('newsletter:entity:error:code'));
}

$entity = get_entity($guid);

// view the newsletter
echo elgg_view_layout('newsletter', [
	'entity' => $entity,
]);
