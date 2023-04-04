<?php

/**
 * Newsletter Template
 */
class NewsletterTemplate extends ElggObject {
	
	const SUBTYPE = 'newsletter_template';
	
	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
	}
}
