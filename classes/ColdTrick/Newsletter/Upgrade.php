<?php

namespace ColdTrick\Newsletter;

class Upgrade {
	
	public static function urlPostfix($event, $type, $object) {
		
		$url_postfix_name = elgg_get_plugin_setting('url_postfix_name', 'newsletter');
		$url_postfix_value = elgg_get_plugin_setting('url_postfix_value', 'newsletter');
		
		if (!is_null($url_postfix_name) && ($url_postfix_name !== false) && !is_null($url_postfix_value) && ($url_postfix_value !== false)) {
			$new_setting =  elgg_get_plugin_setting('url_postfix', 'newsletter');
			
			$new_setting .= "\n{$url_postfix_name}={$url_postfix_value}";
			
			// save old setting to new setting
			elgg_set_plugin_setting('url_postfix', $new_setting, 'newsletter');
			
			// remove old settings
			elgg_unset_plugin_setting('url_postfix_name', 'newsletter');
			elgg_unset_plugin_setting('url_postfix_value', 'newsletter');
		}
	}
}
