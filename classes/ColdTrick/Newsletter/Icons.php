<?php

namespace ColdTrick\Newsletter;

class Icons {

	/**
	 * Extends icon sizes for newsletter header image
	 *
	 * @param \Elgg\Hook $hook 'entity:icon:sizes', 'object'
	 *
	 * @return array
	 */
	public static function extendIconSizes(\Elgg\Hook $hook) {
		if ($hook->getParam('entity_subtype') !== 'newsletter') {
			return;
		}
		
		$returnvalue = $hook->getValue();
		
		$returnvalue['newsletter_header'] = [
			'w' => 600,
			'h' => 240,
			'square' => false,
			'upscale' => true,
			'crop' => true,
		];
	
		return $returnvalue;
	}
}
