<?php

	$english = array(
		// general
		'item:object:newsletter' => "Newsletter",
		'newsletter:add' => "New newsletter",
		'newsletter:subscribe' => "Subscribe",
		'newsletter:unsubscribe' => "Unsubscribe",
		
		// menu's
		'newsletter:menu:site' => "Newsletters",
		
		'newsletter:menu:page:subscriptions' => "My subscriptions",
		'newsletter:menu:page:settings' => "Newsletter subscriptions",
		'' => "",
		
		'newsletter:breadcrumb:site' => "Newsletters",
		
		// pages
		'newsletter:site:title' => "All site newsletters",
		'newsletter:add:title' => "Create a newsletter",
		'newsletter:edit:title' => "Edit newsletter: %s",
		'newsletter:schedule:title' => "%s: Schedule",
		
		// edit
		'newsletter:edit:subject' => "Custom e-mail subject (optional)",
		
		// steps menu
		'newsletter:menu:steps:entity' => "Basic",
		'newsletter:menu:steps:content' => "Content",
		'newsletter:menu:steps:styling' => "Styling",
		'newsletter:menu:steps:recipients' => "Recipients",
		'newsletter:menu:steps:schedule' => "Schedule",
		'newsletter:menu:steps:log' => "Log",

		// content
		'newsletter:edit:content:add_section' => "Add section",
		'newsletter:edit:content:description' => "Add the content to your newsletter here. Add sections of free text or import an existing blog as content into your newsletter. You can always reorder the content of this newsletter.",
		'newsletter:edit:content:section:collapse' => "Collapse",
		'newsletter:edit:content:section:expand' => "Expand",
		'newsletter:edit:content:section:collapse_all' => "Collapse all",
		'newsletter:edit:content:section:expand_all' => "Expand all",

		// template
		'newsletter:edit:template:description' => "Here you can control the newsletter layout by changing the html and control the styling by changing the css. Do not forget to preview you newsletter to see if it looks as expected.",
		'newsletter:edit:template:select' => "Select a template",
		'newsletter:edit:template:select:default' => "Default template",
		'newsletter:edit:template:select:custom' => "Custom template",
		'newsletter:edit:template:html' => "HTML",
		'newsletter:edit:template:css' => "CSS",
	
		// schedule
		'newsletter:schedule:description' => "Here you can configure when the newsletter will be delivered to the selected recipients.",
		'newsletter:schedule:date' => "Scheduled date",
		'newsletter:schedule:time' => "Scheduled time",

		// recipients
		'newsletter:recipients:title' => "Select recipients",
		'newsletter:recipients:description' => "Below you can configure who you want to receive the newsletter.",
		'newsletter:recipients:recipient' => "Search a recipient",
		'newsletter:recipients:recipient:description' => "You can search on name, email and username. Please select one from the dropdown to add the reciepient.",
		'newsletter:recipients' => "Recipients",
		'newsletter:recipients:subscribers' => "To all subscribers",
		'newsletter:recipients:members' => "To all members",
		'newsletter:recipients:members:site' => "site members",
		'newsletter:recipients:members:group' => "group members",
		'newsletter:recipients:email' => "E-mail address",
		
		// plugin settings
		'newsletter:settings:allow_groups' => "Allow group admins to send newsletters",
		'newsletter:settings:allow_groups:description' => "Group administrators can create a newsletter for their group members.",
		'newsletter:settings:opt_out_existing_users' => "Existing users have to opt-out of the newsletters",
		'newsletter:settings:opt_out_existing_users:description' => "When you set this setting to 'yes' users who haven't yet configured their newsletter preferences will receive a newsletter.",
		
		// entity view
		'newsletter:entity:scheduled' => "Scheduled",
		'newsletter:entity:sent' => "Sent",
		
		// my subscriptions
		'newsletter:subscriptions:description' => "Below you can manage all your newsletter subscriptions as once.",
		'newsletter:subscriptions:site:title' => "Site newsletter",
		'newsletter:subscriptions:site:description' => "Do you wish to receive a newsletter from the communty.",
		'newsletter:subscriptions:groups:title' => "My group newsletters",
		'newsletter:subscriptions:groups:description' => "All the groups you're a member of are listed, so you can easily change your newsletter subscription.",
		'newsletter:subscriptions:other:title' => "Other subscription",
		'newsletter:subscriptions:other:description' => "Did you want to receive a newsletter from a group your not a member of, they are listed here.",
		
		// sidebar - steps
		'newsletter:sidebar:steps' => "Steps",

		// unsubscribe
		'newsletter:unsubscribe:error:input' => "Incorrect input, please check the link in your email",
		'newsletter:unsubscribe:error:code' => "Invalid unsubscribe code, please check the link in your email",
		'newsletter:unsubscribe:title' => "Unsubscribe from the newsletter",
		'newsletter:unsubscribe:user' => "Hi %s,

Check the two settings below and click unsubscribe in order to complete the proccess.",
		'newsletter:unsubscribe:email' => "Your email address %s will be unsubscribed if you click the button below.",
		'newsletter:unsubscribe:entity' => "I no longer wish to receive the '%s' newsletter",
		'newsletter:unsubscribe:all' => "I don't want to receive any newsletters from the %s community",
		
		// sidebar - subscribe
		'newsletter:sidebar:subscribe:title' => "Newsletter subscription",
		'newsletter:subscribe:email:description' => "Subscribe to the %s newsletter",
		'newsletter:subscribe:user:description:subscribe' => "Subscribe to the %s newsletter",
		'newsletter:subscribe:user:description:unsubscribe' => "Unsubscribe from the %s newsletter",
		
		// registration
		'newsletter:registration' => "I want to receive site newsletters",
		
		// email content
		'newsletter:subject' => "%s newsletter: %s",
		'newsletter:plain_message' => "In order to correctly view the newsletter your e-mail client needs to support HTML mails.

To view the newsletter online click here:
%s",
		
		// actions
		// edit
		'newsletter:action:edit:error:title' => "Please provide a title for the newsletter",
		'newsletter:action:edit:error:save' => "An unknown error occured while saving the newsletter, please try again",
		'newsletter:action:edit:success' => "The newsletter was saved",
		
		// delete
		'newsletter:action:delete:error:delete' => "An unknown error occured while deleting the newsletter, please try again",
		'newsletter:action:delete:success' => "The newsletter was deleted",
		
		// schedule
		'newsletter:action:schedule:success' => "Schedule saved",
		
		// recipients
		'newsletter:action:recipients:success' => "Recipients saved",
		
		// content
		'newsletter:action:content:success' => "Content saved",
		
		// template
		'newsletter:action:template:success' => "Template saved",
		
		// subscribe
		'newsletter:action:subscribe:error:subscribe' => "An error occured while subscribing, please try again",
		'newsletter:action:subscribe:error:unsubscribe' => "An error occured while unsubscribing, please try again",
		'newsletter:action:subscribe:success' => "You've successfully subscribed to the newsletter",
		'newsletter:action:subscribe:success:unsubscribe' => "You've successfully been unsubscribed from the newsletter",
		
		// subscriptions
		'newsletter:action:subscriptions:error' => "An unknown error occured while saving your subscription settings, please try again",
		'newsletter:action:subscriptions:success' => "Your subscription settings were saved",
		
		'' => "",
	);
	
	add_translation("en", $english);
