<?php

	/**
	 * The cron hook will take care of sending all the scheduled newsletters
	 *
	 * @param string $hook
	 * @param string $type
	 * @param string $returnvalue
	 * @param array $params
	 */
	function newsletter_cron_handler($hook, $type, $returnvalue, $params) {
		
	}
	
	/**
	 * remove some options from the write access array if it's a newsletter
	 *
	 * @param string $hook
	 * @param string $type
	 * @param string $returnvalue
	 * @param array $params
	 * @return array
	 */
	function newsletter_write_access_handler($hook, $type, $returnvalue, $params) {
		$result = $returnvalue;
		
		if (elgg_is_logged_in() && elgg_in_context("newsletter")) {
			$page_owner = elgg_get_page_owner_entity();
			
			if (!empty($page_owner)) {
				$allowed_keys = array(
					ACCESS_PRIVATE,
					ACCESS_LOGGED_IN,
					ACCESS_PUBLIC
				);
				
				if (elgg_instanceof($page_owner, "group")) {
					$allowed_keys[] = $page_owner->group_acl;
				}
				
				foreach ($result as $access_id => $label) {
					if (!in_array($access_id, $allowed_keys)) {
						unset($result[$access_id]);
					}
				}
			}
		}
		
		return $result;
	}