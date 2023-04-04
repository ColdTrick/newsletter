<?php
/**
 * Custom class for Newsletter Subscriptions
 */
class NewsletterSubscription extends ElggObject {
	const SUBSCRIPTION = 'subscribed';
	const BLACKLIST = 'blacklisted';
	const GENERAL_BLACKLIST = 'general_blacklisted';
	const SUBTYPE = 'newsletter_subscription';
	
	/**
	 * {@inheritdoc}
	 */
	public function initializeAttributes() {
		parent::initializeAttributes();
		
		$site = elgg_get_site_entity();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['owner_guid'] = $site->guid;
		$this->attributes['container_guid'] = $site->guid;
		$this->attributes['access_id'] = ACCESS_PRIVATE;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function save(): bool {
		// ignore access restrictions
		return elgg_call(ELGG_IGNORE_ACCESS, function() {
			return parent::save();
		});
	}
}
