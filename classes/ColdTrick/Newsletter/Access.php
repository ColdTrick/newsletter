<?php

namespace ColdTrick\Newsletter;

class Access {

	/**
	 * Remove some options from the write access array if it's a newsletter
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param array  $returnvalue returnvalue of the hook
	 * @param array  $params      params of the hook
	 *
	 * @return array write access array
	 */
	public static function writeAccessCollections($hook, $type, $returnvalue, $params) {
		
		if (!elgg_is_logged_in() || !elgg_in_context('newsletter')) {
			return;
		}
		
		$page_owner = elgg_get_page_owner_entity();
		if (empty($page_owner)) {
			return;
		}
		
		$allowed_keys = [ACCESS_LOGGED_IN, ACCESS_PUBLIC];
		if (elgg_instanceof($page_owner, 'group')) {
			$allowed_keys[] = $page_owner->group_acl;
		}
			
		foreach ($returnvalue as $access_id => $label) {
			if (!in_array($access_id, $allowed_keys)) {
				unset($returnvalue[$access_id]);
			}
		}
	
		return $returnvalue;
	}
}