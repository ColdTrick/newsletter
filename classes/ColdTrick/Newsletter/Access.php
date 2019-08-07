<?php

namespace ColdTrick\Newsletter;

class Access {

	/**
	 * Remove some options from the write access array if it's a newsletter
	 *
	 * @param \Elgg\Hook $hook 'access:collections:write', 'all'
	 *
	 * @return array write access array
	 */
	public static function writeAccessCollections(\Elgg\Hook $hook) {
		
		if (!elgg_is_logged_in() || !elgg_in_context('newsletter')) {
			return;
		}
		
		$allowed_keys = [ACCESS_LOGGED_IN, ACCESS_PUBLIC];
		
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup) {
			$acl = $page_owner->getOwnedAccessCollection('group_acl');
			if (!empty($acl)) {
				$allowed_keys[] = $acl->id;
			}
		}
		
		$returnvalue = $hook->getValue();
		foreach ($returnvalue as $access_id => $label) {
			if (!in_array($access_id, $allowed_keys)) {
				unset($returnvalue[$access_id]);
			}
		}
	
		return $returnvalue;
	}
}
