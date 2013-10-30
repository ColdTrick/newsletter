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
	}