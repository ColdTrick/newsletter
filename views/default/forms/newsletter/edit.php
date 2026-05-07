<?php

$entity = elgg_extract('entity', $vars);

$fields = elgg()->fields->get('object', \Newsletter::SUBTYPE);
foreach ($fields as $field) {
	$name = elgg_extract('name', $field);
	
	switch (elgg_extract('#type', $field)) {
		case 'access':
			if ($entity instanceof \ElggBlog) {
				$field['entity'] = $entity;
			}
		
		// fall through to set value
		default:
			$field['value'] = elgg_extract($name, $vars);
			break;
	}
	
	echo elgg_view_field($field);
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity?->guid,
]);

echo elgg_view_field([
	'#type' => 'container_guid',
	'entity_type' => 'object',
	'entity_subtype' => \Newsletter::SUBTYPE,
	'value' => elgg_extract('container_guid', $vars),
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
