<?php

gatekeeper();

$guid = (int) get_input('guid');

// validate input
if (empty($guid)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}

elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

$newsletter_content = elgg_view_layout('newsletter', ['entity' => $entity]);
echo newsletter_apply_url_postfix($newsletter_content, $entity);

echo elgg_view('newsletter/buttons', ['entity' => $entity, 'type' => 'preview']);