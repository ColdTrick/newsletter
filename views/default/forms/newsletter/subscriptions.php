<?php
/**
 * Manage the subsciptions of a specific user
 *
 * @uses	$vars['entity']	The user to manage the subscriptions of
 */

$entity = elgg_extract('entity', $vars);

$processed_subscriptions = [];

// description
echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:subscriptions:description'), 'class' => 'mtn']);

// site subscription
$site = elgg_get_site_entity();
$processed_subscriptions[] = $site->getGUID();

echo elgg_view('forms/newsletter/subscriptions/site', $vars);

// are group newsletters allowed
if (newsletter_is_group_enabled()) {
	// my group subscriptions
	$my_groups = $entity->getGroups(['limit' => false]);
	
	if (!empty($my_groups)) {
		
		$header_row = elgg_format_element('th', [], '&nbsp;');
		$header_row .= elgg_format_element('th', ['class' => 'newsletter-settings-small'], elgg_echo('on'));
		$header_row .= elgg_format_element('th', ['class' => 'newsletter-settings-small'], elgg_echo('off'));
		$rows = elgg_format_element('tr', [], $header_row);
		
		$group_content = [];
		$group_order = [];
		
		foreach ($my_groups as $group) {
			$processed_subscriptions[] = $group->getGUID();
			
			// check if newsletter is enabled for this group
			if (!newsletter_is_group_enabled($group)) {
				continue;
			}
			
			$group_order[$group->getGUID()] = $group->name;
			
			$has_subscription = newsletter_check_user_subscription($entity, $group);
			
			$group_content_row_data = elgg_format_element('td', [], $group->name);
			$group_content_row_data .= elgg_format_element('td', ['class' => 'newsletter-settings-small'], elgg_format_element('input',[
				'type' => 'radio',
				'name' => "subscriptions[{$group->getGUID()}]",
				'value' => '1',
				'checked' => $has_subscription,
			]));
			
			$group_content_row_data .= elgg_format_element('td', ['class' => 'newsletter-settings-small'], elgg_format_element('input',[
				'type' => 'radio',
				'name' => "subscriptions[{$group->getGUID()}]",
				'value' => '0',
				'checked' => !$has_subscription,
			]));
			
			$group_content[$group->getGUID()] = elgg_format_element('tr', [], $group_content_row_data);
		}
		
		natcasesort($group_order);
		foreach ($group_order as $guid => $dummy) {
			$rows .= $group_content[$guid];
		}
		
		$content = elgg_view('output/longtext', ['value' => elgg_echo('newsletter:subscriptions:groups:description'), 'class' => 'mtn mbs']);
		$content .= elgg_format_element('table', ['class' => 'elgg-table-alt'], $rows);
		
		echo elgg_view_module('info', elgg_echo('newsletter:subscriptions:groups:title'), $content);
	}
}

// other group subscriptions
$subscriptions = elgg_get_entities_from_relationship([
	'types' => ['site', 'group'],
	'relationship' => NewsletterSubscription::SUBSCRIPTION,
	'relationship_guid' => $entity->getGUID(),
	'limit' => false,
	'wheres' => ['(e.guid NOT IN (' . implode(',', $processed_subscriptions) . '))'],
]);

if (!empty($subscriptions)) {
	
	$header_row = elgg_format_element('th', [], '&nbsp;');
	$header_row .= elgg_format_element('th', ['class' => 'newsletter-settings-small'], elgg_echo('on'));
	$header_row .= elgg_format_element('th', ['class' => 'newsletter-settings-small'], elgg_echo('off'));
	$rows = elgg_format_element('tr', [], $header_row);
	
	$subscriptions_order = [];
	$subscriptions_content = [];
	
	foreach ($subscriptions as $subscription) {
		// check if group and enabled or site
		if (elgg_instanceof($subscription, 'site') || (elgg_instanceof($subscription, 'group') && newsletter_is_group_enabled($subscription))) {
			$subscriptions_order[$subscription->getGUID()] = $subscription->name;
						
			$has_subscription = newsletter_check_user_subscription($entity, $subscription);

			$subscription_row_data = elgg_format_element('td', [], $subscription->name);
			$subscription_row_data .= elgg_format_element('td', ['class' => 'newsletter-settings-small'], elgg_view('input/radio', [
				'name' => "subscriptions[{$subscription->getGUID()}]",
				'value' => '1',
				'checked' => $has_subscription,
			]));
				
			$subscription_row_data .= elgg_format_element('td', ['class' => 'newsletter-settings-small'], elgg_view('input/radio', [
				'name' => "subscriptions[{$subscription->getGUID()}]",
				'value' => '0',
				'checked' => !$has_subscription,
			]));
			
			$subscriptions_content[$subscription->getGUID()] = elgg_format_element('tr', [], $subscription_row_data);
		}
	}
	
	natcasesort($subscriptions_order);
	foreach ($subscriptions_order as $guid => $dummy) {
		$rows .= $subscriptions_content[$guid];
	}
	
	$content = elgg_view('output/longtext', ['value' => elgg_echo('newsletter:subscriptions:other:description'), 'class' => 'mtn mbs']);
	$content .= elgg_format_element('table', ['class' => 'elgg-table-alt'], $rows);
	
	echo elgg_view_module('info', elgg_echo('newsletter:subscriptions:other:title'), $content);
}

$foot = elgg_view('input/hidden', ['name' => 'user_guid', 'value' => $entity->getGUID()]);
$foot .= elgg_view('input/submit', ['value' => elgg_echo('save')]);
echo elgg_format_element('div', ['class' => 'elgg-foot'], $foot);
