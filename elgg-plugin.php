<?php

use ColdTrick\Newsletter\Bootstrap;
use Elgg\Router\Middleware\Gatekeeper;

require_once(__DIR__ . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'settings' => [
		'allow_site' => 'yes',
		'allow_groups' => 'no',
		'custom_from' => 'no',
		'include_existing_users' => 'yes',
		'include_banned_users' => 0,
	],
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
	'routes' => [
		'add:object:newsletter' => [
			'path' => 'newsletter/add/{guid}',
			'resource' => 'newsletter/add',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:newsletter' => [
			'path' => 'newsletter/edit/{guid}/{subpage?}',
			'resource' => 'newsletter/edit',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:newsletter:template' => [
			'path' => 'newsletter/edit/{guid}/template',
			'resource' => 'newsletter/edit',
			'defaults' => [
				'subpage' => 'template',
			],
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:newsletter:content' => [
			'path' => 'newsletter/edit/{guid}/content',
			'resource' => 'newsletter/edit',
			'defaults' => [
				'subpage' => 'content',
			],
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:newsletter:recipients' => [
			'path' => 'newsletter/edit/{guid}/recipients',
			'resource' => 'newsletter/edit',
			'defaults' => [
				'subpage' => 'recipients',
			],
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:newsletter:schedule' => [
			'path' => 'newsletter/edit/{guid}/schedule',
			'resource' => 'newsletter/edit',
			'defaults' => [
				'subpage' => 'schedule',
			],
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'log:object:newsletter' => [
			'path' => 'newsletter/log/{guid}',
			'resource' => 'newsletter/log',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'preview:object:newsletter' => [
			'path' => 'newsletter/preview/{guid}',
			'resource' => 'newsletter/preview',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'view:object:newsletter' => [
			'path' => 'newsletter/view/{guid}/{code?}',
			'resource' => 'newsletter/view',
			'walled' => false,
		],
		'collection:object:newsletter:all' => [
			'path' => 'newsletter/site',
			'resource' => 'newsletter/site',
		],
		'collection:object:newsletter:group' => [
			'path' => 'newsletter/group/{guid}/{filter?}',
			'resource' => 'newsletter/group',
		],
		'collection:object:newsletter:received' => [
			'path' => 'newsletter/received/{username}',
			'resource' => 'newsletter/received',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'collection:object:newsletter:site' => [
			'path' => 'newsletter/site/{filter?}',
			'resource' => 'newsletter/site',
		],
		'collection:object:newsletter:subscriptions' => [
			'path' => 'newsletter/subscriptions/{username}',
			'resource' => 'newsletter/subscriptions',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'default:object:newsletter:recipients' => [
			'path' => 'newsletter/recipients',
			'resource' => 'newsletter/recipients',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'default:object:newsletter:embed' => [
			'path' => 'newsletter/embed/{guid}',
			'resource' => 'newsletter/embed',
		],
		'default:object:newsletter:unsubscribe' => [
			'path' => 'newsletter/unsubscribe/{guid}',
			'resource' => 'newsletter/unsubscribe',
			'walled' => false,
		],
		'default:object:newsletter' => [
			'path' => 'newsletter',
			'resource' => 'newsletter/site',
		],
	],
	'actions' => [
		'newsletter/edit' => [],
		'newsletter/edit/schedule' => [],
		'newsletter/edit/content' => [],
		'newsletter/edit/template' => [],
		'newsletter/edit/template_to_custom' => [],
		'newsletter/edit/recipients' => [],
		
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
