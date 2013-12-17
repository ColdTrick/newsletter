<?php

?>
//<script>
elgg.provide("elgg.newsletter");

elgg.newsletter.init = function() {
	$("#newsletter-edit-template-select").live("change", function(event) {
		event.stopPropagation();
		if ($(this).val() == "custom") {
			$(".newsletter-edit-template-custom").show();
		} else {
			$(".newsletter-edit-template-custom").hide();
		}
	});
}

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter.init);