<?php

	class Newsletter extends ElggObject {
		const SUBTYPE = "newsletter";
		const SEND_TO = "send_to";
		
		public function __clone() {
			parent::__clone();
			
			$this->title = elgg_echo("newsletter:duplicate_of") . " " . $this->title;
			$this->status = "concept";
			unset($this->temp_metadata["scheduled"]);
			unset($this->temp_metadata["start_time"]);
		}
		
		protected function initializeAttributes() {
			parent::initializeAttributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
		
		public function getURL() {
			return "newsletter/view/" . $this->getGUID();
		}
		
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
		
		public function getOnlineURL() {
			return elgg_get_site_url($this->site_guid) . "newsletter/view/" . $this->getGUID() . "/online";
		}
	}