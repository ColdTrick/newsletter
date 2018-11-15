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
];
