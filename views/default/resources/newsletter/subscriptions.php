<?php

use Elgg\Exceptions\Http\EntityNotFoundException;
use Elgg\Exceptions\Http\EntityPermissionsException;

$page_owner = elgg_get_page_owner_entity();
if (!$page_owner instanceof ElggUser) {
	throw new EntityNotFoundException();
}
if (!$page_owner->canEdit()) {
	throw new EntityPermissionsException();
}

// set context
elgg_push_context('settings');

// set breadcrumb
elgg_push_breadcrumb(elgg_echo('settings'), elgg_generate_url('settings:account', ['username' => $page_owner->username]));

$content = elgg_view_form('newsletter/subscriptions', [
	'id' => 'newsletter-settings-form',
], [
	'entity' => $page_owner,
]);

// draw page
echo elgg_view_page(elgg_echo('newsletter:menu:page:settings'), [
	'content' => $content,
	'filter' => false,
]);
