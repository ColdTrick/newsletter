<?php
/**
 * Extends the registration form with a subscription question
 */

if (elgg_get_plugin_setting('allow_site', 'newsletter') !== 'yes') {
	return;
}

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('newsletter:registration'),
	'name' => 'newsletter_subscription',
	'value' => '1',
	'switch' => true,
]);
