<?php

namespace ColdTrick\Newsletter;

class Cron {

	/**
	 * The cron hook will take care of sending all the scheduled newsletters
	 *
	 * @param string $hook        name of the hook
	 * @param string $type        type of the hook
	 * @param string $returnvalue returnvalue of the hook
	 * @param array  $params      params of the hook
	 *
	 * @return void
	 */
	public static function sendNewsletters($hook, $type, $returnvalue, $params) {
	
		if (empty($params) || !is_array($params)) {
			return;
		}
		
		$cron_ts = elgg_extract('time', $params, time());

		// check for time drift
		if (date('i', $cron_ts) >= 30) {
			// example of the time: 14:59:56
			// which should be the hourly cron for 15:00:00
			$cron_ts = $cron_ts + (30 * 60);
		}

		// make the timestamp to an hour
		$newsletter_ts = mktime(date('H', $cron_ts), 0, 0, date('n', $cron_ts), date('j', $cron_ts), date('Y', $cron_ts));

		// ignore access
		$ia = elgg_set_ignore_access(true);

		$newsletters = new \ElggBatch('elgg_get_entities_from_metadata', [
			'type' => 'object',
			'subtype' => \Newsletter::SUBTYPE,
			'limit' => false,
			'metadata_name_value_pairs' => [
				'name' => 'scheduled',
				'value' => $newsletter_ts,
			],
		]);
	
		foreach ($newsletters as $newsletter) {
			newsletter_start_commandline_sending($newsletter);
		}
		
		// retore access
		elgg_set_ignore_access($ia);
	}
}