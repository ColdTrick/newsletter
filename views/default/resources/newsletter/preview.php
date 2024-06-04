<?php

$guid = (int) get_input('guid');
elgg_entity_gatekeeper($guid, 'object', \Newsletter::SUBTYPE, true);

/* @var $entity Newsletter */
$entity = get_entity($guid);

elgg_require_css('resources/newsletter/preview');

$container = $entity->getContainerEntity();
if (!$container instanceof \ElggGroup) {
	$container = null;
}

elgg_push_entity_breadcrumbs($entity);

elgg_register_menu_item('title', [
	'name' => 'preview_by_mail',
	'icon' => 'mail',
	'text' => elgg_echo('newsletter:menu:preview_by_mail'),
	'href' => elgg_http_add_url_query_elements('ajax/form/newsletter/preview_mail', [
		'guid' => $entity->guid,
	]),
	'class' => ['elgg-lightbox', 'elgg-button', 'elgg-button-action'],
]);

echo elgg_view_page(elgg_echo('preview'), [
	'content' => elgg_format_element('iframe', [
		'src' => $entity->getURL(),
		'class' => 'newsletter-preview',
	]),
	'sidebar' => false,
	'filter' => false,
	'entity' => $entity,
], 'default', [
	'header_url' => false,
]);
