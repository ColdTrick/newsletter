<?php

/**
 * Form to (un)subscribe to the newsletter of a container entity
 *
 * @uses	$vars['entity']	The container entity to (un)subscribe to
 */

$entity = elgg_extract("entity", $vars);
$user = elgg_get_logged_in_user_entity();

if (!empty($user)) {
	// check if the user is subscribed to container
	if (newsletter_check_user_subscription($user, $entity)) {
		// already subscribed, so offer unsubscibe
		$submit_text = elgg_echo("newsletter:unsubscribe");
		
		echo "<div>";
		echo elgg_echo("newsletter:subscribe:user:description:unsubscribe", array($entity->name));
		echo "</div>";
	} else {
		// not yet so subscribe
		$submit_text = elgg_echo("newsletter:subscribe");
		
		echo "<div>";
		echo elgg_echo("newsletter:subscribe:user:description:subscribe", array($entity->name));
		echo "</div>";
	}
	
	echo elgg_view("input/hidden", array("name" => "user_guid", "value" => $user->getGUID()));
} else {
	// show email subscribe form
	$submit_text = elgg_echo("newsletter:subscribe");
	
	echo "<div>";
	echo elgg_echo("newsletter:subscribe:email:description", array($entity->name));
	echo "<label for='newsletter-subscribe-email' class='hidden'>" . elgg_echo("newsletter:recipients:email") . "</label>";
	echo elgg_view("input/email", array("name" => "email", "id" => "newsletter-subscribe-email", "title" => elgg_echo("newsletter:recipients:email"), "placeholder" => elgg_echo("newsletter:recipients:email")));
	echo "</div>";
}

echo "<div class='float-alt'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/submit", array("value" => $submit_text));
echo "</div>";
