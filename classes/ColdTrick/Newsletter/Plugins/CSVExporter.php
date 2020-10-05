<?php

namespace ColdTrick\Newsletter\Plugins;

class CSVExporter {
	
	/**
	 * Register specific value to be exported by CSV Exporter
	 *
	 * @param \Elgg\Hook $hook 'get_exportable_values', 'csv_exporter'
	 *
	 * @return array
	 */
	public static function exportableValues(\Elgg\Hook $hook) {
		
		$type = $hook->getParam('type');
		$subtype = $hook->getParam('subtype');
		if ($type !== 'object' || $subtype !== \Newsletter::SUBTYPE) {
			return;
		}
		
		$readable = (bool) $hook->getParam('readable', false);
		
		$fields = [
			elgg_echo('newsletter:csv_exporter:from') => 'newsletter_from',
			elgg_echo('newsletter:csv_exporter:status') => 'newsletter_status',
			elgg_echo('newsletter:csv_exporter:scheduled') => 'newsletter_scheduled',
			elgg_echo('newsletter:csv_exporter:scheduled:readable') => 'newsletter_scheduled_readable',
			elgg_echo('newsletter:csv_exporter:endtime') => 'newsletter_endtime',
			elgg_echo('newsletter:csv_exporter:endtime:readable') => 'newsletter_endtime_readable',
		];
		
		$result = $hook->getValue();
		
		if (!$readable) {
			$fields = array_values($fields);
		}
		
		return array_merge($result, $fields);
	}
	
	/**
	 * Export the actual value
	 *
	 * @param \Elgg\Hook $hook 'export_value', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportValue(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \Newsletter) {
			return;
		}
		
		$exportable_value = $hook->getParam('exportable_value');
		switch ($exportable_value) {
			case 'newsletter_from':
				return $entity->from;
				break;
			case 'newsletter_status':
				return elgg_echo("newsletter:status:{$entity->status}");
				break;
			case 'newsletter_scheduled':
				return $entity->scheduled;
				break;
			case 'newsletter_scheduled_readable':
				$scheduled = $entity->scheduled;
				if (!empty($scheduled)) {
					return csv_exported_get_readable_timestamp($entity->scheduled);
				}
				break;
			case 'newsletter_endtime':
				$logging = $entity->getLogging();
				if (!empty($logging)) {
					return elgg_extract('end_time', $logging);
				}
				break;
			case 'newsletter_endtime_readable':
				$logging = $entity->getLogging();
				if (!empty($logging)) {
					$endtime = elgg_extract('end_time', $logging);
					if (!empty($endtime)) {
						return csv_exported_get_readable_timestamp($endtime);
					}
				}
				break;
		}
	}
}
