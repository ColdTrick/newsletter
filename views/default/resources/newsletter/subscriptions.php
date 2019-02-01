<?php

use Elgg\EntityPermissionsException;
use Elgg\EntityNotFoundException;

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

// build page element
$title_text = elgg_echo('newsletter:menu:page:settings');

$content = elgg_view_form('newsletter/subscriptions', [
	'id' => 'newsletter-settings-form',
], [
	'entity' => $page_owner,
]);

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
]);

// draw page
echo elgg_view_page($title_text, $page_data);
