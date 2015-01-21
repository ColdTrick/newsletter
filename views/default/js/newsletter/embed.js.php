<?php ?>
//<script>

elgg.provide("elgg.newsletter");

elgg.newsletter.init = function() {

	$("#newsletter-embed-list li").live("click", function(event) {
		elgg.newsletter.embed_format(this);
		
		event.preventDefault();
	});

	$("#newsletter-embed-pagination a").live("click", function(event) {
		var url = $(this).attr("href");
		$("#newsletter-embed-pagination").parent().load(url);
		event.preventDefault();
	});

	$("#newsletter-embed-search").live("submit", function(event) {

		event.preventDefault();

		var query = $(this).serialize();
		var url = $(this).attr("action");
			
		$(this).parent().load(url, query, function() {
			$.colorbox.resize();
		});
	});
}

elgg.newsletter.embed_format = function(elem) {
	var data = $(elem).find("> div").data();
	
	if (!data) {
		return false;
	}
	
	$("#newsletter-embed-wrapper, #newsletter-embed-format").toggleClass("hidden");
	
	console.log(data);
}

elgg.newsletter.embed_format_submit = function() {
	elgg.newsletter.embed($("#newsletter-embed-format-preview").html());
}

elgg.newsletter.embed = function(content) {

	var textAreaId = $(".elgg-form-newsletter-edit-content textarea").attr("id");
	var textArea = $("#" + textAreaId);
	
	textArea.val(textArea.val() + content);
	textArea.focus();

	<?php
		// See the TinyMCE plugin for an example of this view
		echo elgg_view('embed/custom_insert_js');
	?>

	elgg.ui.lightbox.close();
}

//register init hook
elgg.register_hook_handler("init", "system", elgg.newsletter.init);
