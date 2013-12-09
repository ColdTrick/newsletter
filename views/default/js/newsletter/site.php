<?php

?>
//<script>
elgg.provide("elgg.newsletter");

elgg.newsletter.init = function() {
	$("#newsletter-section-add").click(elgg.newsletter.section_add);
	
	$(".newsletter-section-actions .elgg-icon-delete-alt").live("click", function() {
		elgg.newsletter.section_remove(this);
	});

	$("#newsletter-section-list").sortable({
		containment: "parent",
		handle: ".elgg-icon-cursor-drag-arrow"
	});
}

elgg.newsletter.section_add = function() {
	$('#newsletter-section-add').next().clone().insertBefore($('#newsletter-section-add')).removeClass("hidden");
}

elgg.newsletter.section_remove = function(elem) {
	$(elem).parents(".newsletter-section").remove();
}

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter.init);