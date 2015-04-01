elgg.provide("elgg.newsletter_recipients");

elgg.newsletter_recipients.init = function() {
	$("#newsletter-recipients-autocomplete").autocomplete({
		source: function( request, response ) {
			$.getJSON(elgg.get_site_url() + "newsletter/recipients", {
				term: request.term,
				guid: function() {
					return $("#newsletter-form-recipients input[name='guid']").val();
				}
			}, response);
		},
		minLength: 2,
		html: "html",
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function (event, ui) {
			// clear the search term from the input
			this.value = "";
			var counter = 0;
			
			// put the result in a different location
			switch (ui.item.type) {
				case "user":
					$("#newsletter-recipients-users>.elgg-body").append(ui.item.content);
					$("#newsletter-recipients-users").show();

					counter = $("#newsletter-recipients-users .elgg-body .newsletter-recipient-result").length;
					$("#newsletter-recipients-users .elgg-head .newsletter-counter").html("(" + counter + ")");
					break;
				case "group":
					$("#newsletter-recipients-groups>.elgg-body").append(ui.item.content);
					$("#newsletter-recipients-groups").show();

					counter = $("#newsletter-recipients-groups .elgg-body .newsletter-recipient-result").length;
					$("#newsletter-recipients-groups .elgg-head .newsletter-counter").html("(" + counter + ")");
					break;
				case "email":
					$("#newsletter-recipients-emails>.elgg-body").append(ui.item.content);
					$("#newsletter-recipients-emails").show();

					counter = $("#newsletter-recipients-emails .elgg-body .newsletter-recipient-result").length;
					$("#newsletter-recipients-emails .elgg-head .newsletter-counter").html("(" + counter + ")");
					break;
			}
			
			return false;
		},
		// turn off experimental live help - no i18n support and a little buggy
		messages: {
			noResults: '',
			results: function() {}
		},
		create: function (e) {
			$(this).prev('.ui-helper-hidden-accessible').remove();
		}
	});

	$("#newsletter-recipients-wrapper .elgg-icon-delete-alt").live("click", function() {
		var $container = $(this).parents(".elgg-module-newsletter-recipients");
		
		$(this).parents(".newsletter-recipient-result").remove();

		var counter = 0;
		counter = $container.find(".newsletter-recipient-result").length;
		if (counter > 0) {
			$container.find(".elgg-head .newsletter-counter").html("(" + counter + ")");
		} else {
			$container.find(".elgg-head .newsletter-counter").html("");
			$container.hide();
		}
	});
};

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter_recipients.init);