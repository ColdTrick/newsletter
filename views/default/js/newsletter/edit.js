elgg.provide("elgg.newsletter");

elgg.newsletter.init_edit = function() {
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

	$("#newsletter-send-now").live("click", elgg.newsletter.schedule_send_now);

	$("#newsletter-edit-template-save-as").live("click", elgg.newsletter.save_template);
	
	$("#newsletter-status-notification-toggle").on("click", elgg.newsletter.toggle_status_notification);
	$("#newsletter-status-notification-me").on("click", elgg.newsletter.status_notification_me);
};

elgg.newsletter.schedule_send_now = function() {
	if (confirm(elgg.echo("question:areyousure"))) {
		$("#newsletter-form-schedule").attr("action", elgg.get_site_url() + "action/newsletter/send");
		$("#newsletter-form-schedule").submit();
	}
};

elgg.newsletter.save_template = function() {
	var valid = true;

	$("#newsletter-form-template .newsletter-input-error").removeClass("newsletter-input-error");
	
	if ($("#newsletter-form-template input[name='name']").val() === "") {
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
};

elgg.newsletter.toggle_status_notification = function(event) {
	event.preventDefault();
	
	$(".newsletter-status-notification").toggleClass("hidden");
};

elgg.newsletter.status_notification_me = function() {
	if ($(this).is(":checked")) {
		$("#newsletter-status-notification").val($(this).val());
	} else {
		$("#newsletter-status-notification").val("");
	}
};

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter.init_edit);