<?php

namespace ColdTrick\Newsletter;

/**
 * Icon callbacks
 */
class Icons {

	/**
	 * Extends header sizes for newsletter header image
	 *
	 * @param \Elgg\Event $event 'entity:header:sizes', 'object'
	 *
	 * @return null|array
	 */
	public static function extendHeaderSizes(\Elgg\Event $event): ?array {
		if ($event->getParam('entity_subtype') !== \Newsletter::SUBTYPE) {
			return null;
		}
		
		$returnvalue = $event->getValue();
		
		$returnvalue['newsletter'] = [
			'w' => 600,
			'h' => 240,
			'square' => false,
			'upscale' => true,
			'crop' => true,
		];
		
		return $returnvalue;
	}
}
