<?php

	$english = array(
		// general
		'item:object:newsletter' => "Newsletter",
		'newsletter:add' => "New newsletter",
		
		// menu's
		'newsletter:menu:site' => "Newsletters",
		'' => "",
		
		'newsletter:breadcrumb:site' => "Newsletters",
		'' => "",
		
		// pages
		'newsletter:site:title' => "All site newsletters",
		'newsletter:add:title' => "Create a newsletter",
		'newsletter:edit:title' => "Edit newsletter: %s",
		
		// edit tabs
		'newsletter:edit:tabs:entity' => "Basic",
		'newsletter:edit:tabs:content' => "Content",
		'newsletter:edit:tabs:styling' => "Styling",
		'newsletter:edit:tabs:recipients' => "Recipients",
		'newsletter:edit:tabs:schedule' => "Schedule",
		
		// plugin settings
		'newsletter:settings:allow_groups' => "Allow group admins to send newsletters",
		'newsletter:settings:allow_groups:description' => "Group administrators can create a newsletter for their group members.",
		'newsletter:settings:opt_out_existing_users' => "Existing users have to opt-out of the newsletters",
		'newsletter:settings:opt_out_existing_users:description' => "When you set this setting to 'yes' users who haven't yet configured their newsletter preferences will receive a newsletter.",
		
		// actions
		// edit
		'newsletter:action:edit:error:title' => "Please provide a title for the newsletter",
		'newsletter:action:edit:error:save' => "An unknown error occured while saving the newsletter, please try again",
		'newsletter:action:edit:success' => "The newsletter was saved",
		'' => "",
	);
	
	add_translation("en", $english);