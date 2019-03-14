<?php

class Newsletter extends ElggObject {
	
	const SUBTYPE = 'newsletter';
	const SEND_TO = 'send_to';
	
	/**
	 * Clones the newsletter
	 *
	 * @return void
	 *
	 * @see ElggEntity::__clone()
	 */
	public function __clone() {
		parent::__clone();
		
		$this->title = elgg_echo('newsletter:duplicate_of') . ' ' . $this->title;
		$this->status = 'concept';
		
		unset($this->scheduled);
		unset($this->start_time);
	}
	
	/**
	 * {@inheritDoc}
	 * @see ElggEntity::initializeAttributes()
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		
		$this->status = 'concept';
	}
	
	/**
	 * {@inheritDoc}
	 * @see ElggEntity::getURL()
	 */
	public function getURL() {
		return elgg_normalize_url(elgg_generate_entity_url($this, 'view', null, [
			'code' => newsletter_generate_commanline_secret($this->guid),
		]));
	}
	
	/**
	 * {@inheritDoc}
	 * @see ElggObject::canComment()
	 */
	public function canComment($user_guid = 0, $default = null) {
		return false;
	}
	
	/**
	 * Writes logging data to a file
	 *
	 * @param array $logging data to be saved
	 *
	 * @return false|int
	 */
	public function saveLogging($logging) {
		if (empty($logging)) {
			return false;
		}
		
		$fh = new ElggFile();
		$fh->owner_guid = $this->getGUID();
		$fh->setFilename('logging.json');
		
		$fh->open('write');
		$result = $fh->write(json_encode($logging, JSON_PRETTY_PRINT));
		$fh->close();
		
		return $result;
	}
	
	/**
	 * Returns logging from a file
	 *
	 * @return false|string
	 */
	public function getLogging() {
		
		$fh = new ElggFile();
		$fh->owner_guid = $this->getGUID();
		$fh->setFilename('logging.json');
		
		if (!$fh->exists()) {
			return false;
		}
		
		$contents = $fh->grabFile();
		
		if (empty($contents)) {
			return false;
		}
		
		return json_decode($contents, true);
	}
	
	/**
	 * Save the recipients on disk
	 *
	 * @param array $recipients the recipients config
	 *
	 * @return false|int
	 */
	public function setRecipients($recipients) {
		
		if (!is_array($recipients)) {
			return false;
		}
		
		// check for previous DB recipients
		if ($this->recipients) {
			unset($this->recipients);
		}
		
		$fh = new ElggFile();
		$fh->owner_guid = $this->getGUID();
		$fh->setFilename('recipients.json');
		
		$fh->open('write');
		$result = $fh->write(json_encode($recipients));
		$fh->close();
		
		return $result;
	}
	
	/**
	 * Get the recipients
	 *
	 * @return false|array
	 */
	public function getRecipients() {
		
		// check for previous DB recipients
		if ($this->recipients) {
			$recipients = json_decode($this->recipients, true);
			$this->setRecipients($recipients);
		}
		
		$fh = new ElggFile();
		$fh->owner_guid = $this->getGUID();
		$fh->setFilename('recipients.json');
		
		if (!$fh->exists()) {
			return false;
		}
		
		$raw = $fh->grabFile();
		
		return json_decode($raw, true);
	}
}
