<?php
/**
 * Custom class for Newsletters
 *
 * @var SUBTYPE		The subtype of the newsletters
 * @var SEND_TO		A relationship to link users to this newsletter
 *
 * @package Newsletter
 */
class Newsletter extends ElggObject {
	const SUBTYPE = "newsletter";
	const SEND_TO = "send_to";
	
	/**
	 * Clones the newsletter
	 *
	 * @return void
	 *
	 * @see ElggEntity::__clone()
	 */
	public function __clone() {
		parent::__clone();
		
		$this->title = elgg_echo("newsletter:duplicate_of") . " " . $this->title;
		$this->status = "concept";
		unset($this->temp_metadata["scheduled"]);
		unset($this->temp_metadata["start_time"]);
	}
	
	/**
	 * Initializes attributes for this class
	 *
	 * @return void
	 *
	 * @see ElggObject::initializeAttributes()
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes["subtype"] = self::SUBTYPE;
	}
	
	/**
	 * Returns the url to the newsletter
	 *
	 * @return string url to the newsletter
	 * @see ElggEntity::getURL()
	 */
	public function getURL() {
		return elgg_normalize_url("newsletter/view/" . $this->getGUID() . "/" . newsletter_generate_commanline_secret($this->getGUID()));
	}
	
	/**
	 * Writes logging data to a file
	 *
	 * @param string $logging data to be saved
	 *
	 * @return Ambigous <boolean, number>
	 */
	public function saveLogging($logging) {
		$result = false;
		
		if (!empty($logging)) {
			$fh = new ElggFile();
			$fh->owner_guid = $this->getGUID();
			$fh->setFilename("logging.json");
			
			$fh->open("write");
			$result = $fh->write(json_encode($logging));
			$fh->close();
		}
		
		return $result;
	}
	
	/**
	 * Returns logging from a file
	 *
	 * @return Ambigous <boolean, string>
	 */
	public function getLogging() {
		$result = false;
		
		$fh = new ElggFile();
		$fh->owner_guid = $this->getGUID();
		$fh->setFilename("logging.json");
		
		if ($fh->exists()) {
			$contents = $fh->grabFile();
			
			if (!empty($contents)) {
				$result = json_decode($contents, true);
			}
		}
		
		return $result;
	}
}
