<?php

	class Newsletter extends ElggObject {
		const SUBTYPE = "newsletter";
		
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
		
		public function getOnlineURL() {
			return elgg_get_site_url($this->site_guid) . "newsletter/view/" . $entity->getGUID() . "/online";
		}
	}