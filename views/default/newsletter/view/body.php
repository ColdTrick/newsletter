<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \Newsletter) {
	return;
}

$template = $entity->template ?: 'default';
if (is_numeric($template)) {
	// probably a custom template, lets check
	$template_entity = get_entity($template);
	if ($template_entity instanceof \NewsletterTemplate) {
		$content = $template_entity->html;
	} else {
		// something wrong, reset to default
		$template = 'default';
	}
}

if (!isset($content)) {
	if ($template === 'custom') {
		$content = $entity->html;
	} else {
		if (!elgg_view_exists("newsletter/templates/{$template}/body")) {
			// something wrong, reset to default
			$template = 'default';
		}
		
		$content = elgg_view("newsletter/templates/{$template}/body", $vars);
	}
}

$container_entity = $entity->getContainerEntity();
$container_url = $container_entity->getURL();
if ($container_entity instanceof \ElggSite) {
	$container_url = $container_entity->url;
}

$header = '';
if ($entity->hasIcon('newsletter', 'header')) {
	$header = elgg_format_element('img', [
		'src' => elgg_normalize_url($entity->getIconURL([
			'type' => 'header',
			'size' => 'newsletter',
		])),
		'style' => 'display: block; width: 100%;',
	]);
}

$replacements = [
	'{content}' => $entity->content,
	'{header}' => $header,
	'{unsub}' => elgg_echo('newsletter:body:unsub'),
	'{online}' => elgg_echo('newsletter:body:online'),
	'{title}' => $entity->title,
	'{description}' => $entity->description,
	'{subject}' => $entity->subject,
	'{newsletter_url}' => elgg_normalize_url($entity->getURL()),
	'{site_name}' => elgg_get_site_entity()->getDisplayName(),
	'{site_description}' => elgg_get_site_entity()->description,
	'{site_url}' => elgg_get_site_url(),
	'{container_name}' => $entity->getContainerEntity()?->getDisplayName(),
	'{container_url}' => elgg_normalize_url($container_url),
];

if (PHP_SAPI !== 'cli') {
	// (pre)viewing online
	$user = elgg_get_logged_in_user_entity();
	if ($user instanceof \ElggUser) {
		$replacements['{unsublink}'] = elgg_generate_url('collection:object:newsletter:subscriptions', [
			'username' => $user->username,
		]);
	} else {
		$email = get_input('e');
		if ($email) {
			$replacements['{newsletter_url}'] = $replacements['{newsletter_url}'] . '?e=' . $email;
			$replacements['{unsublink}'] = newsletter_generate_unsubscribe_link($entity->getContainerEntity(), $email);
		} else {
			$replacements['{unsublink}'] = newsletter_generate_unsubscribe_link($entity->getContainerEntity(), '');
		}
	}
}

foreach ($replacements as $search => $replace) {
	$content = str_ireplace($search, (string) $replace, $content);
}

echo $content;
