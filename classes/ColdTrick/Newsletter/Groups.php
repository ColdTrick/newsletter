<?php

namespace ColdTrick\Newsletter;

use Elgg\Hook;
use Elgg\Groups\Tool;

class Groups {
	
	/**
	 * Adds the group tool option
	 *
	 * @param \Elgg\Hook $hook Hook
	 *
	 * @return []
	 */
	public static function registerGroupNewsletterTool(\Elgg\Hook $hook) {
		$plugin = elgg_get_plugin_from_id('newsletter');
		if ($plugin->getSetting('allow_groups') !== 'yes') {
			return;
		}
		
		$result = $hook->getValue();
		
		$result[] = new Tool('newsletter', [
			'label' => elgg_echo('newsletter:group:tool_option'),
			'default_on' => true,
		]);
	
		return $result;
	}
}
