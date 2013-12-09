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
		
		if (!empty($params) && is_array($params)) {
			$cron_ts = elgg_extract("time", $params, time());
			
			// check for time drift
			if (date("i", $cron_ts) >= 30) {
				// example of the time: 14:59:56
				// which should be the hourly cron for 15:00:00
				$cron_ts = $cron_ts + (30 * 60);
			}
			
			// make the timestamp to an hour
			$newsletter_ts = mktime(date("H", $cron_ts), 0, 0, date("n", $cron_ts), date("j", $cron_ts), date("Y", $cron_ts));
			
			$options = array(
				"type" => "object",
				"subtype'" => Newsletter::SUBTYPE,
				"limit" => false,
				"metadata_name_value_pairs" => array(
					"name" => "scheduled",
					"value" => $newsletter_ts
				)
			);
			
			// ignore access
			$ia = elgg_set_ignore_access(true);
			
			$newsletters = elgg_get_entities_from_metadata($options);
			
			// retore access
			elgg_set_ignore_access($ia);
			
			if (!empty($newsletters)) {
				foreach ($newsletters as $newsletter) {
					newsletter_start_commandline_sending($newsletter);
				}
			}
		}
	}
	
	/**
	 * Remove some options from the write access array if it's a newsletter
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
	