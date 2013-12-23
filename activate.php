<?php

/**
 * This file is called when activating the plugin
 *
 */

// update the newsletter subtype to it's own class (when the plugin was active before
if (!update_subtype("object", Newsletter::SUBTYPE, "Newsletter")) {
	// first time the plugin was activated
	add_subtype("object", Newsletter::SUBTYPE, "Newsletter");
}

if (!update_subtype("object", NewsletterSubscription::SUBTYPE, "NewsletterSubscription")) {
	// first time the plugin was activated
	add_subtype("object", NewsletterSubscription::SUBTYPE, "NewsletterSubscription");
}

add_subtype("object", NEWSLETTER_CONTENT_SUBTYPE);
add_subtype("object", NEWSLETTER_TEMPLATE);