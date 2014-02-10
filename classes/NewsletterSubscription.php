<?php
/**
 * Custom class for Newsletter Subscriptions
 *
 * @package Newsletter
 *
 */
class NewsletterSubscription extends ElggObject {
	const SUBSCRIPTION = "subscribed";
	const BLACKLIST = "blacklisted";
	const GENERAL_BLACKLIST = "general_blacklisted";
	
	const SUBTYPE = "newsletter_subscription";
	
	/**
	 * Initializes attributes for this class
	 *
	 * @return void
	 *
	 * @see ElggObject::initializeAttributes()
	 */
	public function initializeAttributes() {
		parent::initializeAttributes();
		
		$site = elgg_get_site_entity();
		
		$this->attributes["subtype"] = self::SUBTYPE;
		$this->attributes["owner_guid"] = $site->getGUID();
		$this->attributes["container_guid"] = $site->getGUID();
		$this->attributes["access_id"] = ACCESS_PRIVATE;
		
	}
	
	/**
	 * Save the subscription to the database
	 *
	 * @return bool
	 *
	 * @see ElggObject::save()
	 */
	public function save() {
		// ignore access restrictions
		$ia = elgg_set_ignore_access(true);
		
		$result = parent::save();
		
		// restore access
		elgg_set_ignore_access($ia);
		
		return $result;
	}
}