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
		if (elgg_get_plugin_setting('allow_groups', 'newsletter') !== 'yes') {
			return null;
		}
		
		$result = $event->getValue();
		
		$result[] = new Tool('newsletter', [
			'default_on' => true,
		]);
		
		return $result;
	}
}
