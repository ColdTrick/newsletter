<?php

/**
 * Extends the registration form with a subscription question
 */

$checked = array();
if (!empty($newsletter_subscription)) {
	$checked = array("checked" => "checked");
}

echo "<div class='mtl'>";
echo elgg_view("input/checkbox", array("name" => "newsletter_subscription", "value" => "1", "id" => "newsletter-registration-subscription") + $checked);
echo "<label for='newsletter-registration-subscription'>" . elgg_echo("newsletter:registration") . "</label>";
echo "</div>";