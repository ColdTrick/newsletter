define(function(require) {
	
	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');
	
	require(['jquery.ui.autocomplete.html']);
	
	$("#newsletter-recipients-autocomplete").autocomplete({
		source: function(request, response) {
			var ajax = new Ajax();
			ajax.path('livesearch/newsletter_recipients', {
				data: {
					view: 'json',
					term: request.term,
					guid: function() {
						return $("#newsletter-form-recipients input[name='guid']").val();
					},
				},
				dataType: 'json',
				success: function(data) {
					response(data);
				}
			});
		},
		minLength: 2,
		html: "html",
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function(event, ui) {
			// clear the search term from the input
			this.value = "";
			var counter = 0;
			
			// put the result in a different location
			switch (ui.item.type) {
				case "user":
					
					$("#newsletter-recipients-users > .elgg-body > .elgg-list").append(ui.item.html);
					$("#newsletter-recipients-users").show();

					counter = $("#newsletter-recipients-users .elgg-body .elgg-item").length;
					$("#newsletter-recipients-users .elgg-head .newsletter-counter").html("(" + counter + ")");
					break;
				case "group":
					$("#newsletter-recipients-groups > .elgg-body > .elgg-list").append(ui.item.html);
					$("#newsletter-recipients-groups").show();

					counter = $("#newsletter-recipients-groups .elgg-body .elgg-item").length;
					$("#newsletter-recipients-groups .elgg-head .newsletter-counter").html("(" + counter + ")");
					break;
				case "email":
					$("#newsletter-recipients-emails > .elgg-body > .elgg-list").append(ui.item.html);
					$("#newsletter-recipients-emails").show();

					counter = $("#newsletter-recipients-emails .elgg-body .elgg-item").length;
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
		create: function(e) {
			$(this).prev('.ui-helper-hidden-accessible').remove();
		}
	});

	$(document).on('click', '.elgg-autocomplete-item-remove', function() {
		var $container = $(this).closest('.elgg-module');
		
		$(this).closest('li.elgg-item').remove();

		var counter = 0;
		counter = $container.find('.elgg-body .elgg-item').length;
		if (counter > 0) {
			$container.find('.elgg-head .newsletter-counter').html('(' + counter + ')');
		} else {
			$container.find('.elgg-head .newsletter-counter').html('');
			$container.hide();
		}
	});
});
