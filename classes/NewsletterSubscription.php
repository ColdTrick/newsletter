<?php

class NewsletterSubscription extends ElggObject {
	const SUBSCRIPTION = "subscribed";
	const BLACKLIST = "blacklisted";
	const GENERAL_BLACKLIST = "general_blacklisted";
	
	const SUBTYPE = "newsletter_subscription";
	
	public function initializeAttributes() {
		parent::initializeAttributes();
		
		$site = elgg_get_site_entity();
		
		$this->attributes["subtype"] = self::SUBTYPE;
		$this->attributes["owner_guid"] = $site->getGUID();
		$this->attributes["container_guid"] = $site->getGUID();
		$this->attributes["access_id"] = ACCESS_PRIVATE;
		
	}
}