<?php

$entity = $vars["entity"];

$template = $entity->template;
if (empty($template)) {
	$template = "default";
}

if ($template == "custom") {
	$content = $entity->html;
} else {
	$content = elgg_view("newsletter/templates/" . $template . "/body");
}


$container_entity = $entity->getContainerEntity();
$container_url = $container_entity->getURL();
if ($container_entity instanceof ElggSite) {
	$container_url = $container_entity->url;
}

$replacements = array(
		"{content}" => $entity->content,
		
		"{unsub}" => elgg_echo("newsletter:body:unsub"),
		"{online}" => elgg_echo("newsletter:body:online"),
		
		"{title}" => $entity->title,
		"{description}" => $entity->description,
		"{subject}" => $entity->subject,
		"{newsletter_url}" => $entity->getURL(),
		"{site_name}" => elgg_get_site_entity()->name,
		"{site_url}" => elgg_get_site_url(),
		"{container_name}" => $entity->getContainerEntity()->name,
		"{container_url}" => $container_url,
	);

foreach ($replacements as $search => $replace) {
	$content = str_ireplace($search, $replace, $content);
}

echo $content;