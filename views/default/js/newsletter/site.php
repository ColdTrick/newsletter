<?php

?>
//<script>
elgg.provide("elgg.newsletter");

elgg.newsletter.init = function() {
	$("#newsletter-section-add").live("click", function(event) {
		event.preventDefault();
		elgg.newsletter.section_add(event);
	});
	
	$(".newsletter-section-actions .elgg-icon-delete-alt").live("click", function() {
		elgg.newsletter.section_remove(this);
	});

	$("#newsletter-section-list").sortable({
		items: "> .newsletter-section",
		forcePlaceholderSize: true,
		placeholder: "newsletter-sorting-placeholder",
		//containment: "parent",
		handle: ".elgg-icon-cursor-drag-arrow:first",
		//tolerance: "pointer",
		start:  function(event, ui) {
			elgg.newsletter.toggle_drag(true);
		},
		stop:  function(event, ui) {
			elgg.newsletter.toggle_drag(false);
		},
		update: function(event, ui) {
				elgg.newsletter.content_save();
			},
		connectWith: "#newsletter-section-list .newsletter-sub-section"
	});

	$("#newsletter-section-list .newsletter-sub-section").sortable({
		items: "> .newsletter-section",
		forcePlaceholderSize: true,
		placeholder: "newsletter-sorting-placeholder",
		//containment: "#newsletter-section-list",
		handle: ".elgg-icon-cursor-drag-arrow:first",
		//tolerance: "pointer",
		start:  function(event, ui) {
			elgg.newsletter.toggle_drag(true);
		},
		stop:  function(event, ui) {
			elgg.newsletter.toggle_drag(false);
		},
		update: function(event, ui) {
				elgg.newsletter.content_save();
			},
		connectWith: "#newsletter-section-list, #newsletter-section-list .newsletter-sub-section"
	});
	
	$("#newsletter-section-list").delegate(".elgg-input-text, .elgg-input-plaintext", "blur", function(event) {
		elgg.newsletter.content_save();
	});

	$(".newsletter-actions-edit").live("click", function() {
		$description = $(this).parent().parent().parent().find("[name='description']");
		$description.toggle();
		if ($description.is(":visible")) {
			$(this).html(elgg.echo("newsletter:edit:content:section:collapse"));
		} else {
			$(this).html(elgg.echo("newsletter:edit:content:section:expand"));
		}
	});

	$(".newsletter-expand-all").live("click", function(event) {
		event.preventDefault();
		$("#newsletter-section-list [name='description']").show();
		$("#newsletter-section-list .newsletter-actions-edit").html(elgg.echo("newsletter:edit:content:section:collapse"));
	});

	$(".newsletter-collapse-all").live("click", function(event) {
		event.preventDefault();
		$("#newsletter-section-list [name='description']").hide();
		$("#newsletter-section-list .newsletter-actions-edit").html(elgg.echo("newsletter:edit:content:section:expand"));
	});
}

elgg.newsletter.toggle_drag = function(on) {
	if (on) {
		$("#newsletter-section-list").addClass("newsletter-sorting");
	} else {
		$("#newsletter-section-list").removeClass("newsletter-sorting");
	}
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