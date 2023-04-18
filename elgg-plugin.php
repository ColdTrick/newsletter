<?php

use Elgg\Router\Middleware\Gatekeeper;
use Elgg\Router\Middleware\GroupPageOwnerGatekeeper;
use Elgg\Router\Middleware\UserPageOwnerCanEditGatekeeper;
use ColdTrick\Newsletter\Upgrades\MoveHeaderIcons;

require_once(__DIR__ . '/lib/functions.php');

return [
	'plugin' => [
		'version' => '9.0.1',
		'dependencies' => [
			'ckeditor' => [],
		],
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'newsletter',
			'class' => \Newsletter::class,
			'capabilities' => [
				'commentable' => false,
				'searchable' => true,
				'likable' => true,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'newsletter_subscription',
			'class' => \NewsletterSubscription::class,
			'capabilities' => [
				'commentable' => false,
				'searchable' => false,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'newsletter_template',
			'class' => \NewsletterTemplate::class,
			'capabilities' => [
				'commentable' => false,
				'searchable' => false,
			],
		],
	],
	'settings' => [
		'allow_site' => 'yes',
		'allow_groups' => 'no',
		'custom_from' => 'no',
		'include_existing_users' => 'yes',
		'include_banned_users' => 0,
		'allow_copy_template' => true,
	],
	'upgrades' => [
		MoveHeaderIcons::class,
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
			'middleware' => [
				GroupPageOwnerGatekeeper::class,
			],
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
				UserPageOwnerCanEditGatekeeper::class,
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
	'events' => [
		'access:collections:write' => [
			'all' => [
				'ColdTrick\Newsletter\Access::writeAccessCollections' => ['priority' => 700], // needs to be after groups
			],
		],
		'container_logic_check' => [
			'object' => [
				'ColdTrick\Newsletter\Permissions::containerLogic' => [],
			],
		],
		'cron' => [
			'hourly' => [
				'ColdTrick\Newsletter\Cron::sendNewsletters' => [],
			],
		],
		'entity:header:sizes' => [
			'object' => [
				'ColdTrick\Newsletter\Icons::extendHeaderSizes' => [],
			],
		],
		'entity:url' => [
			'object' => [
				'ColdTrick\Newsletter\Widgets::widgetURL' => [],
			],
		],
		'export_value' => [
			'csv_exporter' => [
				'ColdTrick\Newsletter\Plugins\CSVExporter::exportValue' => [],
			],
		],
		'get_exportable_values' => [
			'csv_exporter' => [
				'ColdTrick\Newsletter\Plugins\CSVExporter::exportableValues' => [],
			],
		],
		'notification_type_subtype' => [
			'tag_tools' => [
				'ColdTrick\Newsletter\Plugins\TagTools::notificationTypeSubtype' => [],
			],
		],
		'register' => [
			'menu:entity' => [
				'ColdTrick\Newsletter\Menus::entityRegister' => [],
			],
			'menu:filter:newsletter' => [
				'ColdTrick\Newsletter\Menus::filterRegister' => [],
			],
			'menu:filter:newsletter/group' => [
				'ColdTrick\Newsletter\Menus::filterRegister' => [],
			],
			'menu:filter:newsletter_steps' => [
				'ColdTrick\Newsletter\Menus::newsletterSteps' => [],
			],
			'menu:owner_block' => [
				'ColdTrick\Newsletter\Menus::ownerBlockRegister' => [],
			],
			'menu:page' => [
				'ColdTrick\Newsletter\Menus::pageRegister' => [],
			],
			'menu:site' => [
				'ColdTrick\Newsletter\Menus::siteRegister' => [],
			],
			'user' => [
				'ColdTrick\Newsletter\User::subscribeToSiteNewsletter' => [],
			],
		],
		'tool_options' => [
			'group' => [
				'\ColdTrick\Newsletter\Plugins\Groups::registerGroupNewsletterTool' => [],
			],
		],
		'usersettings:save' => [
			'user' => [
				'ColdTrick\Newsletter\User::convertEmailSubscriptionToUserSetting' => [],
			],
		],
		'view' => [
			'page/layouts/newsletter' => [
				'ColdTrick\Newsletter\Plugins\DeveloperTools::reenableLogOutput' => [],
			],
		],
		'view_vars' => [
			'page/layouts/newsletter' => [
				'ColdTrick\Newsletter\Plugins\DeveloperTools::preventLogOutput' => [],
			],
		],
	],
	'view_extensions' => [
		'register/extend' => [
			'newsletter/register' => [],
		],
	],
	'view_options' => [
		'forms/newsletter/preview_mail' => ['ajax' => true],
		'forms/newsletter/subscribe' => ['ajax' => true],
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
