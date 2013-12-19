<?php

?>
//<script>
elgg.provide("elgg.newsletter");

elgg.newsletter.init = function() {
	$("#newsletter-edit-template-select").live("change", function(event) {
		event.stopPropagation();
		if ($(this).val() == "custom") {
			$(".newsletter-edit-template-custom").show();
			$("#newsletter-edit-template-save-as").show();
		} else {
			$(".newsletter-edit-template-custom").hide();
			$("#newsletter-edit-template-save-as").hide();
		}
	});

	$("#newsletter-embed-list li").live("click", function(event) {
		elgg.newsletter.embed(this);
		
		event.preventDefault();
	});

	$("#newsletter-send-now").live("click", elgg.newsletter.schedule_send_now);

	$("#newsletter-edit-template-save-as").live("click", elgg.newsletter.save_template);
}

elgg.newsletter.embed = function(elem) {

	var content = $(elem).html();
	
	var textAreaId = $(".elgg-form-newsletter-edit-content textarea").attr("id");
	var textArea = $("#" + textAreaId);
	
	textArea.val(textArea.val() + content);
	textArea.focus();

	<?php
		// See the TinyMCE plugin for an example of this view
		echo elgg_view('embed/custom_insert_js');
	?>

	$.fancybox.close();
}

elgg.newsletter.schedule_send_now = function() {
	if (confirm(elgg.echo("question:areyousure"))) {
		$("#newsletter-form-schedule").attr("action", elgg.get_site_url() + "action/newsletter/send");
		$("#newsletter-form-schedule").submit();
	}
}

elgg.newsletter.save_template = function() {
	var valid = true;

	$("#newsletter-form-template .newsletter-input-error").removeClass("newsletter-input-error");
	
	if ($("#newsletter-form-template input[name='name']").val() == "") {
		valid = false;
		$("#newsletter-form-template input[name='name']").addClass("newsletter-input-error");
	}

	if (valid) {
		$("#newsletter-form-template input[name='guid']").attr("name", "newsletter_guid");
		$("#newsletter-form-template").attr("action", elgg.get_site_url() + "action/newsletter/template/edit");
		$("#newsletter-form-template").submit();
	} else {
		alert(elgg.echo("newsletter:edit:template:error:save_as"));
	}
}

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter.init);
