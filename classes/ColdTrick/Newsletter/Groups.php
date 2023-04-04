<?php

namespace ColdTrick\Newsletter;

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
	 * @return []
	 */
	public static function registerGroupNewsletterTool(\Elgg\Event $event) {
		$plugin = elgg_get_plugin_from_id('newsletter');
		if ($plugin->getSetting('allow_groups') !== 'yes') {
			return;
		}
		
		$result = $event->getValue();
		
		$result[] = new Tool('newsletter', [
			'default_on' => true,
		]);
	
		return $result;
	}
}
