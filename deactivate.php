<?php

/**
 * This file is called when the plugin is deactivated
 */

// unregister the class handler of the Newsletter
update_subtype("object", Newsletter::SUBTYPE);
update_subtype("object", NewsletterSubscription::SUBTYPE);