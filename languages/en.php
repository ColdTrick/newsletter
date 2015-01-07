<?php

	$english = array(
		// general
		'item:object:newsletter' => "Newsletter",
		'item:object:newsletter_template' => "Newsletter template",
		'item:object:newsletter_subscription' => "Newsletter subscription",
		'newsletter:add' => "New newsletter",
		'newsletter:subscribe' => "Subscribe",
		'newsletter:unsubscribe' => "Unsubscribe",
		'newsletter:duplicate_of' => "Duplicate of",
		
		'newsletter:status:concept' => "Concept",
		'newsletter:status:sending' => "Sending",
		'newsletter:status:scheduled' => "Scheduled",
		'newsletter:status:sent' => "Sent",
		
		// CSV upload
		'newsletter:csv:no_email' => "No e-mail column could be found in the first 2 rows of the CSV-file",
		'newsletter:csv:added' => "Added %s e-mail addresses from the CSV-file",
		
		// menu's
		'newsletter:menu:site' => "Newsletters",
		
		'newsletter:menu:page:subscriptions' => "My subscriptions",
		'newsletter:menu:page:settings' => "Newsletter subscriptions",
		'newsletter:menu:page:received' => "My received newsletters",
		
		'newsletter:menu:entity:log' => "View log",
		'newsletter:menu:entity:duplicate' => "Duplicate",
		
		'newsletter:menu:owner_block:group' => "Group newsletters",
		
		'newsletter:menu:filter:sent' => "Sent",
		'newsletter:menu:filter:concept' => "Concept",
		'newsletter:menu:filter:scheduled' => "Scheduled",
		'newsletter:menu:filter:sending' => "Sending",
		
		// steps menu
		'newsletter:menu:steps:entity' => "Basic",
		'newsletter:menu:steps:content' => "Content",
		'newsletter:menu:steps:template' => "Template",
		'newsletter:menu:steps:recipients' => "Recipients",
		'newsletter:menu:steps:schedule' => "Schedule",
			
		// long text menu
		'newsletter:menu:longtext:embed_blog' => "Embed blog",
		
		'newsletter:breadcrumb:site' => "Newsletters",
		'newsletter:breadcrumb:log' => "Delivery log",
		'newsletter:breadcrumb:received' => "Received",
		
		// pages
		'newsletter:site:title' => "All site newsletters",
		'newsletter:add:title' => "Create a newsletter",
		'newsletter:edit:title' => "Edit newsletter: %s",
		'newsletter:schedule:title' => "%s: Schedule",
		'newsletter:received:title' => "%s's received newsletters",
		'newsletter:received:title:mine' => "My received newsletters",
		
		// edit
		'newsletter:edit:subject' => "Custom e-mail subject (optional)",
		
		// placeholders
		'newsletter:placeholders:info' => "You can use the following placeholders in your text. They will be replaced by system text or by information about the newsletter. If you hover over them, you will get some more details.",
		'newsletter:placeholders:content' => "Content will be replaced by the content step of the newsletter (do not use on the content page)",
		'newsletter:placeholders:unsub' => "Unsub will be replaced by a default unsubcribe text containing a unsubscribe link",
		'newsletter:placeholders:unsublink' => "Unsublink will be replaced by a unsubscribe link",
		'newsletter:placeholders:online' => "Online will be replaced by a default text on where to view the newsletter online",
		'newsletter:placeholders:title' => "Title will be replaced by the title of the newsletter",
		'newsletter:placeholders:description' => "Description will be replaced by the description of the newsletter",
		'newsletter:placeholders:subject' => "Subject will be replaced by the subject of the newsletter",
		'newsletter:placeholders:newsletter_url' => "Newsletter_url will be replaced by the url to the newsletter",
		'newsletter:placeholders:site_name' => "Site_name will be replaced by the name of the site",
		'newsletter:placeholders:site_description' => "Site_description will be replaced by the description of the site",
		'newsletter:placeholders:site_url' => "Site_url will be replaced by the url of the site",
		'newsletter:placeholders:container_name' => "Container_name will be replaced by the name of the container (can be group or site)",
		'newsletter:placeholders:container_url' => "Container_url will be replaced by the url of the container (can be group or site)",
			
		// content
		'newsletter:edit:content:description' => "Add the content to your newsletter here. Add free text or import an existing blog as content into your newsletter.",
					
		// template
		'newsletter:edit:template:description' => "Here you can control the newsletter layout by changing the html and control the styling by changing the css. Do not forget to preview you newsletter to see if it looks as expected.",
		'newsletter:edit:template:copy_to_custom' => "Copy to custom",
		'newsletter:edit:template:copy_to_custom:confirm' => "This action will override the current custom template. Are you sure you wish to copy this template to the custom template for this newsletter?",
		'newsletter:edit:template:select' => "Select a template",
		'newsletter:edit:template:select:default' => "Default template",
		'newsletter:edit:template:select:default2' => "Default template (with sidebar)",
		'newsletter:edit:template:select:custom' => "Custom template",
		'newsletter:edit:template:html' => "HTML",
		'newsletter:edit:template:css' => "CSS",
		'newsletter:edit:template:name' => "Name for the custom template (only needed for saving template)",
		'newsletter:edit:template:save_as' => "Save as template",
		'newsletter:edit:template:error:save_as' => "Please check the marked required fields",
			
		// default template body
		'newsletter:body:unsub' => "This newsletter is sent from <a href='{site_url}' rel='nofollow'>{site_name}</a>. Click <a href='{unsublink}'>here</a> to unsubscribe from this newsletter.",
		'newsletter:body:online' => "If you can't read this newsletter, check it <a href='{newsletter_url}'>online</a>",
			
	
		// schedule
		'newsletter:schedule:description' => "Here you can configure when the newsletter will be delivered to the selected recipients.",
		'newsletter:schedule:date' => "Scheduled date",
		'newsletter:schedule:time' => "Scheduled time",
		'newsletter:schedule:status_notification' => "E-mail address for status notification (optional)",
		'newsletter:schedule:status_notification:description' => "When the newsletter is sent, a notification will be send to this e-mail address.",
		'newsletter:schedule:show_in_archive' => "Show the newsletter in the archive",
		'newsletter:schedule:send' => "Send now",

		// recipients
		'newsletter:recipients:title' => "Select recipients",
		'newsletter:recipients:description' => "Below you can configure who you want to receive the newsletter.",
		'newsletter:recipients:csv' => "Upload a CSV-file with recipient e-mail addresses",
		'newsletter:recipients:csv:description' => "You can upload a CSV file that contains the e-mail addresses for recipients. The text delimiter for the file needs to be \" (double quote) and the column delimiter needs to be a ; (semicolon). The system will auto-detect the e-mail column by searching the first 2 rows.",
		'newsletter:recipients:recipient' => "Search a recipient",
		'newsletter:recipients:recipient:description' => "You can search on name, email and username. Please select one from the dropdown to add the recipient.",
		'newsletter:recipients' => "Recipients",
		'newsletter:recipients:subscribers' => "To all subscribers",
		'newsletter:recipients:members' => "To all members",
		'newsletter:recipients:members:site' => "site members",
		'newsletter:recipients:members:group' => "group members",
		'newsletter:recipients:email' => "E-mail address",
		
		// plugin settings
		'newsletter:settings:allow_groups' => "Allow group admins to send newsletters",
		'newsletter:settings:allow_groups:description' => "Group administrators can create a newsletter for their group members.",
		'newsletter:settings:include_existing_users' => "Include users without subscription settings",
		'newsletter:settings:include_existing_users:description' => "When this setting is set to 'no', all existing users without subscription settings will no longer receive newsletters.",
		
		// entity view
		'newsletter:entity:scheduled' => "Scheduled",
		'newsletter:entity:sent' => "Sent",
		'newsletter:entity:error:code' => "Invalid or missing code to view this newsletter online",
		
		// my subscriptions
		'newsletter:subscriptions:description' => "Below you can manage all your newsletter subscriptions as once.",
		'newsletter:subscriptions:site:title' => "Site newsletter",
		'newsletter:subscriptions:site:description' => "Do you wish to receive a newsletter from the communty.",
		'newsletter:subscriptions:groups:title' => "My group newsletters",
		'newsletter:subscriptions:groups:description' => "All the groups you're a member of are listed, so you can easily change your newsletter subscription.",
		'newsletter:subscriptions:other:title' => "Other subscription",
		'newsletter:subscriptions:other:description' => "Did you want to receive a newsletter from a group your not a member of, they are listed here.",
		
		// unsubscribe
		'newsletter:unsubscribe:error:input' => "Incorrect input, please check the link in your email",
		'newsletter:unsubscribe:error:code' => "Invalid unsubscribe code, please check the link in your email",
		'newsletter:unsubscribe:title' => "Unsubscribe from the newsletter",
		'newsletter:unsubscribe:user' => "Hi %s,

Check the two settings below and click unsubscribe in order to complete the proccess.",
		'newsletter:unsubscribe:email' => "Your email address %s will be unsubscribed if you click the button below.",
		'newsletter:unsubscribe:email:empty' => "Enter your email address and click the button below to unsubscribe.",
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
		
		// status notification
		'newsletter:status_notification:subject' => "Newsletter status notification",
		'newsletter:status_notification:message' => "LS,

Your newsletter '%s' was sent out.

To view the newsletter click here:
%s",
		
		// logging
		'newsletter:log:title' => "Delivery log: %s",
		'newsletter:log:counter:success' => "sent",
		'newsletter:log:counter:error' => "errors",
		
		'newsletter:log:users:title' => "Users",
		'newsletter:log:users:header:email' => "E-mail address",
		'newsletter:log:users:header:time' => "Time",
		'newsletter:log:users:header:status' => "Status",
		'newsletter:log:users:header:guid' => "User",
		
		'newsletter:log:emails:title' => "E-mail addresses",
		'newsletter:log:email:header:email' => "E-mail address",
		'newsletter:log:email:header:time' => "Time",
		'newsletter:log:email:header:status' => "Status",
		
		'newsletter:log:general:title' => "General information",
		'newsletter:log:general:scheduled' => "Scheduled time",
		'newsletter:log:general:starttime' => "Actual starting time",
		'newsletter:log:general:endtime' => "Proccessing finished",
		
		'newsletter:log:no_contents' => "No log file could be found, are you sure this newsletter is already sent.",
		'newsletter:log:no_recipients' => "There were no recipients for this newsletter",
		'newsletter:log:emails:no_recipients' => "There were no individual e-mail addresses in the proccessing of the newsletter. If you had selected some e-mail addresses they could have unsubscribed from the newsletter.",
		'newsletter:log:users:no_recipients' => "There were no registered users in the proccessing of the newsletter. If you had selected some users they could have unsubscribed from the newsletter.",
		
		// group
		'newsletter:group:tool_option' => "Enable group newsletters",
		'newsletter:group:error:not_enabled' => "Newsletters aren't enabled for this group",
		'newsletter:group:title' => "%s's newsletters",
		
		// widget
		'newsletter:widget:subscribe:description' => "Show a quick widget to subscribe to the newsletter",
		
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

		// template to custom
		'newsletter:action:template_to_custom:success' => "Template copied to custom",
		
		// subscribe
		'newsletter:action:subscribe:error:subscribe' => "An error occured while subscribing, please try again",
		'newsletter:action:subscribe:error:unsubscribe' => "An error occured while unsubscribing, please try again",
		'newsletter:action:subscribe:success' => "You've successfully subscribed to the newsletter",
		'newsletter:action:subscribe:success:unsubscribe' => "You've successfully been unsubscribed from the newsletter",
		
		// subscriptions
		'newsletter:action:subscriptions:error' => "An unknown error occured while saving your subscription settings, please try again",
		'newsletter:action:subscriptions:success' => "Your subscription settings were saved",
		
		// send
		'newsletter:action:send:success' => "The newsletter is being send",
		
		// duplicate
		'newsletter:action:duplicate:error' => "An unknown error occured while duplicating the newsletter, please try again",
		'newsletter:action:duplicate:success' => "The newsletter was duplicated",
		
		// template - edit
		'newsletter:action:template:edit:error' => "An unknown error occured while saving the template",
		'newsletter:action:template:edit:success' => "The template was saved",
		
		// template - delete
		'newsletter:action:template:delete:error:delete' => "An unknown error occured while deleting the newsletter template, please try again",
		'newsletter:action:template:delete:success' => "The newsletter template was deleted",
		
		// preview mail
		'newsletter:action:preview_mail:success' => "Mail sent, check your inbox",
		
		// unsubscribe
		'newsletter:action:unsubscribe:error:recipient' => "Invalild recipient to unsubscribe",
		'newsletter:action:unsubscribe:error:all' => "An error occured while unsubscribing you from all newsletters, please try again",
		'newsletter:action:unsubscribe:error:entity' => "An error occured while unsubscribing you from the %s newsletter, please try again",
		'newsletter:action:unsubscribe:success:all' => "You've been unsubscribed from all newsletters",
		'newsletter:action:unsubscribe:success:entity' => "You've been unsubscribed from the %s newsletter",
		
		'' => "",
	);
	
	add_translation("en", $english);
