<?php

/* @var $page_owner \ElggUser */
$page_owner = elgg_get_page_owner_entity();

elgg_push_context('settings');

elgg_push_breadcrumb(elgg_echo('settings'), elgg_generate_url('settings:account', ['username' => $page_owner->username]));

echo elgg_view_page(elgg_echo('newsletter:menu:page:settings'), [
	'content' => elgg_view_form('newsletter/subscriptions', ['id' => 'newsletter-settings-form'], ['entity' => $page_owner]),
	'filter' => false,
	'show_owner_block' => false,
]);
