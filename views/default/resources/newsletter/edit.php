<?php
/**
 * Edit an existing newsletter
 */

use Elgg\EntityPermissionsException;
use ColdTrick\Newsletter\EditForm;

$guid = (int) elgg_extract('guid', $vars);

// validate input
elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);

/* @var $entity Newsletter */
$entity = get_entity($guid);
if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

$subpage = elgg_extract('subpage', $vars, 'basic');

// set page owner
$container = $entity->getContainerEntity();
if (!$container instanceof ElggGroup) {
	elgg_set_page_owner_guid(false);
	
	$container = null;
}

// breadcrumb
elgg_push_collection_breadcrumbs('object', Newsletter::SUBTYPE, $container);
elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());

// build page elements
$title_text = elgg_echo('newsletter:edit:title', [$entity->getDisplayName()]);

$form = new EditForm($entity);

switch ($subpage) {
	case 'template':
	case 'content':
	case 'recipients':
	case 'schedule':
		$form_vars = ['id' => "newsletter-form-{$subpage}"];
		
		$content = elgg_view_form("newsletter/edit/{$subpage}", $form_vars, $form($subpage));
		break;
	case 'basic':
	default:
		$subpage = 'basic';
		
		$content = elgg_view_form('newsletter/edit', [], $form($subpage));
		break;
}

if (!empty($entity->content)) {
	// only show preview if content available
	elgg_register_menu_item('title', ElggMenuItem::factory([
		'name' => 'preview',
		'icon' => 'eye',
		'text' => elgg_echo('preview'),
		'href' => elgg_generate_entity_url($entity, 'preview'),
		'link_class' => 'elgg-button elgg-button-action',
	]));
}

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
	'filter_id' => 'newsletter_steps',
	'filter_value' => $subpage,
	'filter_entity' => $entity,
]);

// draw page
echo elgg_view_page($title_text, $page_data);
