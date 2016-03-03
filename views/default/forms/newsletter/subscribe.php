<?php
/**
 * Form to (un)subscribe to the newsletter of a container entity
 *
 * @uses	$vars['entity']	The container entity to (un)subscribe to
 */

$entity = elgg_extract('entity', $vars);
$user = elgg_get_logged_in_user_entity();

if (!empty($user)) {
	// check if the user is subscribed to container
	if (newsletter_check_user_subscription($user, $entity)) {
		// already subscribed, so offer unsubscibe
		$submit_text = elgg_echo('newsletter:unsubscribe');
		
		echo elgg_format_element('div', [], elgg_echo('newsletter:subscribe:user:description:unsubscribe', [$entity->name]));
	} else {
		// not yet so subscribe
		$submit_text = elgg_echo('newsletter:subscribe');
		
		echo elgg_format_element('div', [], elgg_echo('newsletter:subscribe:user:description:subscribe', [$entity->name]));
	}
	
	echo elgg_view('input/hidden', ['name' => 'user_guid', 'value' => $user->getGUID()]);
} else {
	// show email subscribe form
	$submit_text = elgg_echo('newsletter:subscribe');
	
	$email_input = elgg_echo('newsletter:subscribe:email:description', [$entity->name]);
	$email_input .= elgg_format_element('label', ['for' => 'newsletter-subscribe-email', 'class' => 'hidden'], elgg_echo('newsletter:recipients:email'));
	$email_input .= elgg_view('input/email', [
		'name' => 'email',
		'id' => 'newsletter-subscribe-email',
		'title' => elgg_echo('newsletter:recipients:email'),
		'placeholder' => elgg_echo('newsletter:recipients:email'),
	]);
	echo elgg_format_element('div', [], $email_input);
}

$foot = elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);
$foot .= elgg_view('input/submit', ['value' => $submit_text]);
echo elgg_format_element('div', ['class' => 'float-alt'], $foot);
