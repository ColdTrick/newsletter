<?php

use Elgg\Exceptions\Http\EntityPermissionsException;

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', \Newsletter::SUBTYPE);

/* @var $entity Newsletter */
$entity = get_entity($guid);
if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

elgg_require_css('resources/newsletter/preview');

$container = $entity->getContainerEntity();
if (!$container instanceof \ElggGroup) {
	$container = null;
}

elgg_push_collection_breadcrumbs('object', \Newsletter::SUBTYPE, $container);

elgg_register_menu_item('title', [
	'name' => 'preview_by_mail',
	'icon' => 'mail',
	'text' => elgg_echo('newsletter:menu:preview_by_mail'),
	'href' => 'ajax/form/newsletter/preview_mail?guid=' . $entity->guid,
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
