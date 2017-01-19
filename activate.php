<?php
/**
 * This file is called when activating the plugin
 *
 */

if (get_subtype_id('object', Newsletter::SUBTYPE)) {
	// update the newsletter subtype to it's own class (when the plugin was actived before)
	update_subtype('object', Newsletter::SUBTYPE, 'Newsletter');
} else {
	// first time the plugin was activated
	add_subtype('object', Newsletter::SUBTYPE, 'Newsletter');
}

if (get_subtype_id('object', NewsletterSubscription::SUBTYPE)) {
	update_subtype('object', NewsletterSubscription::SUBTYPE, 'NewsletterSubscription');
} else {
	// first time the plugin was activated
	add_subtype('object', NewsletterSubscription::SUBTYPE, 'NewsletterSubscription');
}

add_subtype('object', NEWSLETTER_CONTENT_SUBTYPE);
add_subtype('object', NEWSLETTER_TEMPLATE);
