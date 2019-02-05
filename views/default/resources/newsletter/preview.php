<?php

use Elgg\EntityPermissionsException;

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
/* @var $entity Newsletter */
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

$container = $entity->getContainerEntity();
if (!$container instanceof ElggGroup) {
	$container = null;
}

elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $container);

elgg_register_menu_item('title', [
	'name' => 'preview_by_mail',
	'icon' => 'mail',
	'text' => elgg_echo('newsletter:menu:preview_by_mail'),
	'href' => 'ajax/form/newsletter/preview_mail?guid=' . $entity->guid,
	'class' => ['elgg-lightbox', 'elgg-button', 'elgg-button-action'],
]);

$title_text = elgg_echo('preview');

$body = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => elgg_format_element('iframe', [
		'src' => $entity->getURL(),
		'class' => 'newsletter-preview',
	]),
	'sidebar' => false,
	'entity' => $entity,
]);

echo elgg_view_page($title_text, $body);
