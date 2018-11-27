<?php

use ColdTrick\Newsletter\Bootstrap;

require_once(__DIR__ . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'newsletter',
			'class' => Newsletter::class,
			'searchable' => true,
		],
		[
			'type' => 'object',
			'subtype' => 'newsletter_subscription',
			'class' => NewsletterSubscription::class,
		],
		[
			'type' => 'object',
			'subtype' => 'newsletter_template',
			'class' => NewsletterTemplate::class,
		],
	],
	'actions' => [
		'newsletter/edit' => [],
		'newsletter/edit/schedule' => [],
		'newsletter/edit/content' => [],
		'newsletter/edit/template' => [],
		'newsletter/edit/template_to_custom' => [],
		'newsletter/edit/recipients' => [],
		
		'newsletter/delete' => [],
		'newsletter/send' => [],
		'newsletter/subscribe' => [
			'access' => 'public',
		],
		'newsletter/unsubscribe' => [
			'access' => 'public',
		],
		'newsletter/subscriptions' => [],
		'newsletter/duplicate' => [],
		'newsletter/preview_mail' => [],
		
		'newsletter/template/edit' => [],
		'newsletter/template/delete' => [],
	],
	'widgets' => [
		'newsletter_subscribe' => [
			'context' => ['index','groups'],
		],
	],
];
