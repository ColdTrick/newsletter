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
		handle: ".elgg-icon-cursor-drag-arrow:first",
		update: function(event, ui) {
				elgg.newsletter.content_save();
			},
		connectWith: "#newsletter-section-list .newsletter-sub-section"
	});

	$("#newsletter-section-list .newsletter-sub-section").sortable({
		containment: "#newsletter-section-list",
		handle: ".elgg-icon-cursor-drag-arrow:first",
		update: function(event, ui) {
				elgg.newsletter.content_save();
			},
		connectWith: "#newsletter-section-list, #newsletter-section-list .newsletter-sub-section"
	});
	
	$("#newsletter-section-list").delegate(".elgg-input-text, .elgg-input-plaintext", "blur", function(event) {
		elgg.newsletter.content_save();
	});
}

elgg.newsletter.section_add = function() {
	$('#newsletter-section-add').next().clone().insertBefore($('#newsletter-section-add')).removeClass("hidden");
}

elgg.newsletter.section_remove = function(elem) {
	$(elem).parents(".newsletter-section").remove();
	elgg.newsletter.content_save();
}

elgg.newsletter.content_save = function() {

	var data = elgg.newsletter.generate_section($("#newsletter-section-list"));
	
	elgg.action("newsletter/edit/content", {
		data: {
			"sections": data,
			"guid": $("#newsletter-section-list input[name='guid']").val()
		}
	});
}

elgg.newsletter.generate_section = function(elem) {
	var data = {};
	
	$(elem).find("> .newsletter-section").each(function(index) {
		data[index] = {
			    title: $(this).find("[name='title']").val(),
			    description: $(this).find("[name='description']").val()
			};
	});

	return data;
}

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter.init);