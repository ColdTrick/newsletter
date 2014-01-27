<?php

$messages = null;
if (count_messages()) {
	// get messages - try for errors first
	$messages = system_messages(NULL, "error");
	if (count($messages["error"]) == 0) {
		// no errors so grab rest of messages
		$messages = system_messages(null, "");
	} else {
		// we have errors - clear out remaining messages
		system_messages(null, "");
	}
}

$type = elgg_extract("type", $vars, "view");
$entity = elgg_extract("entity", $vars);

$menu = elgg_view_menu("newsletter_buttons", array("entity" => $entity, "type" => $type, "class" => "newsletter-buttons", "sort_by" => "priority"));

echo $menu;

echo elgg_view('page/elements/messages', array('object' => $messages));
?>

<style type="text/css">
	.elgg-system-messages {
		position: absolute;
		top: 20px;
		left: 20px;
		margin: 0;
		list-style: none;
	}
	
	.elgg-system-messages .elgg-state-success {
		background: black;
	}
	
	.elgg-system-messages .elgg-state-error {
		background: red;
	}
	
	.elgg-system-messages .elgg-message {
		padding: 5px 10px;
		
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		
		color: white;
		font-weight: bold;
	}
	
	.elgg-system-messages p {
		margin: 0;
	}
	
	.hidden {
		display: none;
	}

	.newsletter-buttons {
		position: absolute;
		top: 20px;
		right: 20px;
		
		margin: 0;
		
		list-style: none;
		text-align: right;
	}
	
	.newsletter-buttons li {
		display: inline-block;
		margin-left: 10px;
	}
	
	.newsletter-buttons li.elgg-menu-item-mail {
		display: block;
		margin-top: 10px;
		margin-left: 0;
	}
	
	.newsletter-buttons li.elgg-menu-item-mail .elgg-button-submit {
		margin-left: 5px;
	}
	
	.newsletter-buttons fieldset {
		border: none;
		padding: 0;
		margin: 0;
	}
	
	.newsletter-buttons a,
	.elgg-button-submit {
		font-size: 14px;
		font-weight: bold;
		
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	
		width: auto;
		cursor: pointer;

		background: #ccc url(<?php echo elgg_get_site_url(); ?>_graphics/button_background.gif) repeat-x 0 0;
		border:1px solid #999;
		color: #333;
		padding: 2px 15px;
		text-align: center;
		text-decoration: none;
		text-shadow: 0 1px 0 white;
	}
	
	.newsletter-buttons a:hover,
	.newsletter-buttons a:focus,
	.elgg-button-submit:hover,
	.elgg-button-submit:focus {
		background: #ccc url(<?php echo elgg_get_site_url(); ?>_graphics/button_background.gif) repeat-x 0 -15px;
		color: #111;
		text-decoration: none;
		border: 1px solid #999;
	}
</style>
