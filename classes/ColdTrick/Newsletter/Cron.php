<?php

namespace ColdTrick\Newsletter;

use Elgg\Values;

class Cron {

	/**
	 * The cron hook will take care of sending all the scheduled newsletters
	 *
	 * @param \Elgg\Hook $hook 'cron', 'hourly'
	 *
	 * @return void
	 */
	public static function sendNewsletters(\Elgg\Hook $hook) {
		
		echo 'Starting newsletter processing' . PHP_EOL;
		elgg_log('Starting newsletter processing', 'NOTICE');
		
		$cron_ts = $hook->getParam('time', time());
		
		$ts = Values::normalizeTime($cron_ts);
		
		// check for time drift
		if ((int) $ts->format('i') >= 30) {
			// example of the time: 14:59:56
			// which should be the hourly cron for 15:00:00
			$ts->modify('+30 minutes');
		}
		
		// make the timestamp to an hour
		$ts = Values::normalizeTime($ts->format('Y-m-d H:00:00 e'));
		
		// ignore access
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($ts) {
			$newsletters = elgg_get_entities([
				'type' => 'object',
				'subtype' => \Newsletter::SUBTYPE,
				'limit' => false,
				'metadata_name_value_pairs' => [
					'name' => 'scheduled',
					'value' => $ts->getTimestamp(),
				],
				'batch' => true,
			]);
			
			foreach ($newsletters as $newsletter) {
				newsletter_start_commandline_sending($newsletter);
			}
		});
		
		echo 'Done with newsletter processing' . PHP_EOL;
		elgg_log('Done with newsletter processing', 'NOTICE');
	}
}