<?php

$entity = $vars["entity"];

$content = $entity->styling_html;

$replacements = array(
		"{content}" => $entity->content,
		"{title}" => $entity->title,
		"{description}" => $entity->description,
		"{subject}" => $entity->subject,
		"{newsletter_url}" => $entity->getURL(),
		"{unsub}" => elgg_echo("newsletter:body:unsub"),
		"{site_name}" => elgg_get_site_entity()->name,
		"{site_url}" => elgg_get_site_entity()->getURL(),
		"{container_name}" => $entity->getContainerEntity()->name,
		"{container_url}" => $entity->getContainerEntity()->getURL(),
	);

foreach ($replacements as $search => $replace) {
	$content = str_ireplace($search, $replace, $content);
}

echo $content;