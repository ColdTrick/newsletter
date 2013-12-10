<?php

	/**
	 * A JS file for the recipient selection of a newsletter
	 *
	 */
?>
//<script>
elgg.provide("elgg.newsletter_recipients");

elgg.newsletter_recipients.init = function() {
	$("#newsletter-recipients-autocomplete").autocomplete({
		source: function( request, response ) {
			

			$.getJSON(elgg.get_site_url() + "newsletter/recipients", {
				term: request.term,
				user_guids: function() {
					var result = new Array();
					
					$("#newsletter-recipients-users input[name='user_guids[]']").each(function(index, elem) {
						result.push($(elem).val());
					});

					return result;
				},
				group_guids: function() {
					var result = new Array();
					
					$("#newsletter-recipients-groups input[name='group_guids[]']").each(function(index, elem) {
						result.push($(elem).val());
					});

					return result;
				},
				emails: function() {
					var result = new Array();
					
					$("#newsletter-recipients-emails input[name='emails[]']").each(function(index, elem) {
						result.push($(elem).val());
					});

					return result;
				},
				guid: function() {
					return $("#newsletter-recipients-form input[name='guid']").val();
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

			// put the result in a different location
			switch (ui.item.type) {
				case "user":
					$("#newsletter-recipients-users>.elgg-body").append(ui.item.content);
					$("#newsletter-recipients-users").show();

					var counter = 0;
					counter = $("#newsletter-recipients-users .elgg-body .newsletter-recipient-result").length;
					$("#newsletter-recipients-users .elgg-head .newsletter-counter").html("(" + counter + ")")
					break;
				case "group":
					$("#newsletter-recipients-groups>.elgg-body").append(ui.item.content);
					$("#newsletter-recipients-groups").show();

					var counter = 0;
					counter = $("#newsletter-recipients-groups .elgg-body .newsletter-recipient-result").length;
					$("#newsletter-recipients-groups .elgg-head .newsletter-counter").html("(" + counter + ")")
					break;
				case "email":
					$("#newsletter-recipients-emails>.elgg-body").append(ui.item.content);
					$("#newsletter-recipients-emails").show();

					var counter = 0;
					counter = $("#newsletter-recipients-emails .elgg-body .newsletter-recipient-result").length;
					$("#newsletter-recipients-emails .elgg-head .newsletter-counter").html("(" + counter + ")")
					break;
			}
			
			return false;
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
}

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter_recipients.init);