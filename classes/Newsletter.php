<?php

	class Newsletter extends ElggObject {
		const SUBTYPE = "newsletter";
		
		protected function initializeAttributes() {
			parent::initializeAttributes();
			
			$this->attributes["subtype"] = self::SUBTYPE;
		}
	}