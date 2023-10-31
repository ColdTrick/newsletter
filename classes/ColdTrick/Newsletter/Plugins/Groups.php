<?php

namespace ColdTrick\Newsletter\Plugins;

use Elgg\Collections\Collection;
use Elgg\Groups\Tool;

/**
 * Groups related callbacks
 */
class Groups {
	
	/**
	 * Adds the group tool option
	 *
	 * @param \Elgg\Event $event 'tool_options', 'group'
	 *
	 * @return null|Collection
	 */
	public static function registerGroupNewsletterTool(\Elgg\Event $event): ?Collection {
		$plugin = elgg_get_plugin_from_id('newsletter');
		if ($plugin->getSetting('allow_groups') !== 'yes') {
			return null;
		}
		
		$result = $event->getValue();
		
		$result[] = new Tool('newsletter', [
			'default_on' => true,
		]);
		
		return $result;
	}
}
