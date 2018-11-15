<?php

class NewsletterTemplate extends ElggObject {
	
	const SUBTYPE = 'newsletter_template';
	
	/**
	 * {@inheritDoc}
	 * @see ElggEntity::initializeAttributes()
	 */
	protected function initializeAttributes() {
		
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
	}
}
