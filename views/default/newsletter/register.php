<?php
/**
 * Extends the registration form with a subscription question
 */

$output = elgg_view('input/checkbox', [
	'name' => 'newsletter_subscription',
	'value' => '1',
	'id' => 'newsletter-registration-subscription',
]);
$output .= elgg_format_element('label', ['for' => 'newsletter-registration-subscription'], elgg_echo('newsletter:registration'));
echo elgg_format_element('div', ['class' => 'mtl'], $output);