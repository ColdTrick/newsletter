<?php

?>
//<script>
elgg.provide("elgg.newsletter");

elgg.newsletter.init = function() {
	// init
	$('#newsletter-section-add').click(elgg.newsletter.section_add);

	$('#newsletter-section-list').sortable({
		containment: "parent",
		handle: ".elgg-icon-cursor-drag-arrow"
	});
}

elgg.newsletter.section_add = function() {
	$('#newsletter-section-add').next().clone().insertBefore($('#newsletter-section-add')).removeClass("hidden");
}

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter.init);