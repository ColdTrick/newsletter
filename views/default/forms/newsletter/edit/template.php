<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Newsletter) {
	return;
}

elgg_require_js('forms/newsletter/edit/template');

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

// get the available templates for this container
$template_options = newsletter_get_available_templates($entity->container_guid, $entity);
if (empty($template_options) || !is_array($template_options)) {
	$template_options = [];
}
natcasesort($template_options);

// select the correct template
$template = 'default';
if ($entity->template) {
	$template = $entity->template;
}

// make sure we can select a template
if (!array_key_exists($template, $template_options)) {
	$template = 'default';
}

echo elgg_view('output/longtext', [
	'value' => elgg_echo('newsletter:edit:template:description'),
]);

$allow_copy_template = (bool) elgg_extract('allow_copy_template', $vars, true);
$template_selector = [
	'#type' => 'radio',
	'#label' => elgg_echo('newsletter:edit:template:select'),
	'id' => 'newsletter-edit-template-select',
	'name' => 'template',
	'value' => $template,
	'options_values' => [],
];

$confirm_options = [
	'confirm' => elgg_echo('newsletter:edit:template:copy_to_custom:confirm'),
	'icon' => 'copy',
	'text' => elgg_echo('newsletter:edit:template:copy_to_custom'),
	'class' => 'mlm hidden',
];
$delete_options = [
	'confirm' => elgg_echo('deleteconfirm'),
	'icon' => 'delete',
	'text' => elgg_echo('delete'),
	'class' => 'mlm hidden',
];

foreach ($template_options as $name => $label) {
	$options = [
		'text' => $label,
		'value' => $name,
	];
	
	if ($name !== 'custom') {
		if ($allow_copy_template) {
			// copy this template to a new custom template
			$confirm_options['href'] = elgg_generate_action_url('newsletter/edit/template_to_custom', [
				'guid' => $entity->guid,
				'template' => $name
			]);
			$options['text'] .= elgg_view('output/url', $confirm_options);
		}
		
		if (is_numeric($name)) {
			// custom template can be removed
			$delete_options['href'] = elgg_generate_action_url('newsletter/template/delete', [
				'guid' => $name,
			]);
			$options['text'] .= elgg_view('output/url', $delete_options);
		}
	}
	
	$template_selector['options_values'][] = $options;
}

echo elgg_view_field($template_selector);

$using_custom_template = $template === 'custom';
$custom_template_class = ['newsletter-edit-template-custom'];
if (!$using_custom_template) {
	$custom_template_class[] = 'hidden';
}

echo elgg_view_field([
	'#type' => 'fieldset',
	'#label' => elgg_echo('newsletter:edit:template:custom:label'),
	'#class' => $custom_template_class,
	'fields' => [
		[
			'#type' => 'plaintext',
			'#label' => elgg_echo('newsletter:edit:template:html'),
			'#help' => elgg_view('newsletter/placeholders'),
			'name' => 'html',
			'value' => $entity->html,
			'required' => true,
			'disabled' => !$using_custom_template,
		],
		[
			'#type' => 'plaintext',
			'#label' => elgg_echo('newsletter:edit:template:css'),
			'name' => 'css',
			'value' => $entity->css,
			'required' => true,
			'disabled' => !$using_custom_template,
		],
		[
			'#type' => 'text',
			'#label' => elgg_echo('newsletter:edit:template:name'),
			'name' => 'name',
		],
	],
]);

// footer
$save_as_options = [
	'#type' => 'button',
	'value' => elgg_echo('newsletter:edit:template:save_as'),
	'id' => 'newsletter-edit-template-save-as',
	'class' => ['elgg-button-action'],
];
if (!$using_custom_template) {
	$save_as_options['#class'][] = 'hidden';
}

$footer = elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'submit',
			'value' => elgg_echo('save'),
		],
		$save_as_options,
	],
]);
elgg_set_form_footer($footer);
