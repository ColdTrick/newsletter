<?php

/**
 * Newsletter Template
 *
 * @property string $css  CSS of the newsletter template
 * @property string $html HTML of the newsletter template
 */
class NewsletterTemplate extends \ElggObject {
	
	const SUBTYPE = 'newsletter_template';
	
	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
	}
}
