<?php

namespace ColdTrick\Newsletter;

use Elgg\Values;

/**
 * Cron callbacks
 */
class Cron {

	/**
	 * The cron event will take care of sending all the scheduled newsletters
	 *
	 * @param \Elgg\Event $event 'cron', 'hourly'
	 *
	 * @return void
	 */
	public static function sendNewsletters(\Elgg\Event $event): void {
		$cron_ts = $event->getParam('time', time());
		
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
	}
}
