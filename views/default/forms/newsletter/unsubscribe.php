<?php

/**
 * Form content for the unsubscribe action
 * Users get here by a link in their emailed newsletter
 *
 * @uses	$vars['entity']		The container to unsubscribe from (primairly)
 * @uses	$vars['recipient']	The recipient of the newsletter (user_guid or email address)
 * @uses	$vars['code']		The validation code, to be used in the action
 */

$entity = elgg_extract('entity', $vars);
$recipient = elgg_extract('recipient', $vars);

if (is_numeric($recipient)) {
	$user = get_user($recipient);
	
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:unsubscribe:user', [$user->name])]);
} elseif (newsletter_is_email_address($recipient)) {
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:unsubscribe:email', [$recipient])]);
} elseif (!$recipient) {
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:unsubscribe:email:empty')]);
	echo elgg_view('input/text', ['name' => 'recipient']) . '<br /><br />';
}

echo '<div>';
// unsubscribe from the provided container
echo elgg_view('input/checkbox', [
	'name' => 'guid',
	'value' => $entity->getGUID(),
	'checked' => true,
	'id' => 'newsletter-unsubscribe-entity',
]);
echo elgg_format_element('label', ['for' => 'newsletter-unsubscribe-entity'], elgg_echo('newsletter:unsubscribe:entity', [$entity->name]));
echo '<br />';
// unsubscribe from all newsletters
echo elgg_view('input/checkbox', [
	'name' => 'all',
	'value' => '1',
	'id' => 'newsletter-unsubscribe-all',
]);
echo elgg_format_element('label', ['for' => 'newsletter-unsubscribe-all'], elgg_echo('newsletter:unsubscribe:all', [elgg_get_site_entity()->name]));

echo '</div>';

$foot = elgg_view('input/hidden', ['name' => 'code', 'value' => elgg_extract('code', $vars)]);
$foot .= elgg_view('input/hidden', ['name' => 'entity_guid', 'value' => $entity->getGUID()]);
if ($recipient) {
	$foot .= elgg_view('input/hidden', ['name' => 'recipient', 'value' => $recipient]);
}
$foot .= elgg_view('input/submit', ['value' => elgg_echo('newsletter:unsubscribe')]);
echo elgg_format_element('div', ['class' => 'elgg-foot'], $foot);