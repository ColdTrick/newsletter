<?php
/**
 * Edit an existing newsletter
 */

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', \Newsletter::SUBTYPE, true);

/* @var $entity \Newsletter */
$entity = get_entity($guid);

elgg_require_css('resources/newsletter/edit');

$subpage = elgg_extract('subpage', $vars, 'basic');

// set page owner
$container = $entity->getContainerEntity();
if (!$container instanceof \ElggGroup) {
	elgg_set_page_owner_guid(0);
	
	$container = null;
}

// breadcrumb
elgg_push_entity_breadcrumbs($entity);

switch ($subpage) {
	case 'template':
	case 'content':
	case 'recipients':
	case 'schedule':
		$form_vars = [
			'id' => "newsletter-form-{$subpage}",
			'prevent_double_submit' => false,
			'sticky_enabled' => true,
		];
		
		$content = elgg_view_form("newsletter/edit/{$subpage}", $form_vars, [
			'entity' => $entity,
		]);
		break;
	case 'basic':
	default:
		$subpage = 'basic';
		
		$content = elgg_view_form('newsletter/edit', ['sticky_enabled' => true], [
			'entity' => $entity,
		]);
		break;
}

if (!empty($entity->content)) {
	// only show preview if content available
	elgg_register_menu_item('title', \ElggMenuItem::factory([
		'name' => 'preview',
		'icon' => 'eye',
		'text' => elgg_echo('preview'),
		'href' => elgg_generate_entity_url($entity, 'preview'),
		'link_class' => 'elgg-button elgg-button-action',
	]));
}

// draw page
echo elgg_view_page(elgg_echo('newsletter:edit:title', [$entity->getDisplayName()]), [
	'content' => $content,
	'filter_id' => 'newsletter_steps',
	'filter_value' => $subpage,
	'filter_entity' => $entity,
]);
