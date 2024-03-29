<?php
/**
 * View a newsletter online
 *
 * @uses get_input('guid') the guid of the newsletter
 */

use Elgg\Exceptions\Http\ValidationException;

$guid = (int) get_input('guid');
$code = get_input('code');

elgg_entity_gatekeeper($guid, 'object', \Newsletter::SUBTYPE);

if (empty($code) || !newsletter_validate_commandline_secret($guid, $code)) {
	throw new ValidationException(elgg_echo('newsletter:entity:error:code'));
}

echo elgg_view_layout('newsletter', [
	'entity' => get_entity($guid),
	'filter' => false,
]);
