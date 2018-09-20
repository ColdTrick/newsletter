<?php

$entity = elgg_extract('entity', $vars);

// get the available tempaltes for this container
$template_options = newsletter_get_available_templates($entity->getContainerGUID(), $entity);
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

echo elgg_view('output/text', ['value' => elgg_echo('newsletter:edit:template:description')]);

$selector = elgg_format_element('div', [], elgg_format_element('label', ['for' => 'newsletter-edit-template-select'], elgg_echo('newsletter:edit:template:select')));

$confirm_options = [
	'confirm' => elgg_echo('newsletter:edit:template:copy_to_custom:confirm'),
	'text' => elgg_echo('newsletter:edit:template:copy_to_custom'),
	'class' => 'mlm hidden elgg-discoverable',
];
$delete_options = [
	'confirm' => elgg_echo('deleteconfirm'),
	'text' => elgg_view_icon('delete-alt'),
	'class' => 'mlm hidden elgg-discoverable',
];

$list_items = '';
foreach ($template_options as $name => $label) {

	$input = elgg_format_element('input', [
		'id' => 'newsletter-edit-template-select',
		'class' => 'elgg-input-radio',
		'type' => 'radio',
		'value' => $name,
		'checked' => ($name == $template),
		'name' => 'template',
	]);
	$template_selector = elgg_format_element('label', [], $input . $label);
	
	if ($name !== 'custom') {
		$confirm_options['href'] = 'action/newsletter/edit/template_to_custom?guid=' . $entity->guid . '&template=' . $name;
		$template_selector .= elgg_view('output/url', $confirm_options);
		
		if (is_numeric($name)) {
			$delete_options['href'] = 'action/newsletter/template/delete?guid=' . $name;
			$template_selector .= elgg_view('output/url', $delete_options);
		}
	}
	$list_items .= elgg_format_element('li', ['class' => 'elgg-discover'], $template_selector);
}
$selector .= elgg_format_element('ul', ['id' => 'newsletter-edit-template-select', 'class' => 'elgg-input-radios elgg-vertical'], $list_items);

echo elgg_format_element('div', ['class' => 'mtm'], $selector);

$custom_template_class = ['newsletter-edit-template-custom'];
if ($entity->template !== 'custom') {
	$custom_template_class[] = 'hidden';
}

$custom_template = '';

$custom_template_html = elgg_format_element('label', ['for' => 'html'], elgg_echo('newsletter:edit:template:html'));
$custom_template_html .= elgg_view('input/plaintext', ['name' => 'html', 'value' => $entity->html]);

$custom_template .= elgg_format_element('div', [], $custom_template_html);

$custom_template .= elgg_view('newsletter/placeholders');

$custom_template_css = elgg_format_element('label', ['for' => 'css'], elgg_echo('newsletter:edit:template:css'));
$custom_template_css .= elgg_view('input/plaintext', ['name' => 'css', 'value' => $entity->css]);

$custom_template .= elgg_format_element('div', ['class' => 'mtm'], $custom_template_css);

$custom_template_name = elgg_format_element('label', ['for' => 'name'], elgg_echo('newsletter:edit:template:name'));
$custom_template_name .= elgg_view('input/text', ['name' => 'name']);

$custom_template .= elgg_format_element('div', [], $custom_template_name);

echo elgg_format_element('div', ['class' => $custom_template_class], $custom_template);

$foot = elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);
$save_as_options = [
	'value' => elgg_echo('newsletter:edit:template:save_as'),
	'id' => 'newsletter-edit-template-save-as',
	'class' => ['elgg-button-action', 'float-alt'],
];
if ($entity->template !== 'custom') {
	$save_as_options['class'][] = 'hidden';
}
$foot .= elgg_view('input/button', $save_as_options);
$foot .= elgg_view('input/submit', ['value' => elgg_echo('save')]);
echo elgg_format_element('div', ['class' => 'elgg-foot'], $foot);
