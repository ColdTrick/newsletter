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
		$("#newsletter-embed-pagination").parent().parent().load(url);
		event.preventDefault();
	});

	$("#newsletter-embed-search").live("submit", function(event) {

		event.preventDefault();

		var query = $(this).serialize();
		var url = $(this).attr("action");
			
		$(this).parent().parent().load(url, query, function() {
			$.colorbox.resize();
		});
	});

	$("#newsletter-embed-format-description, #newsletter-embed-format-icon").live("change", function() {
		elgg.newsletter.embed_format_preview();
	});
}

elgg.newsletter.embed_format = function(elem) {
	var data = $(elem).find("> div").data();
	if (!data) {
		return false;
	}
	
	$("#newsletter-embed-format-icon").parent().hide();
	if (data.iconUrl) {
		$("#newsletter-embed-format-icon").parent().show();
	}
		
	$("#newsletter-embed-wrapper, #newsletter-embed-format").toggleClass("hidden");
	
	$("#newsletter-embed-format-preview").data(data);

	elgg.newsletter.embed_format_preview();
}

elgg.newsletter.embed_format_preview = function() {
	var $preview = $("#newsletter-embed-format-preview");
	var data = $preview.data();
	var content = "";
	var content_description = "";
	var content_icon = "";

	var description_option = $("#newsletter-embed-format-description").val();
	var icon_option = $("#newsletter-embed-format-icon").val();
	
	
 	if (description_option === "full") {
 	 	content_description += data.description;
 	} else if (description_option === "excerpt") {
 		content_description += data.excerpt;
 		content_description += "<p class='newsletter-embed-item-read-more'><a href='" + data.url + "'>" + elgg.echo("newsletter:embed:read_more") + " ></a></p>"; 
 	}

 	if (data.iconUrl) {
		if (icon_option === "left" || icon_option === "right") {
			content_icon += "<img src='" + data.iconUrl + "' />";
		}
 	}
 	
 	content += "<table class='newsletter-embed-item'><tr>";

 	if (content_icon) {
		if (icon_option === "left") {
			content += "<td class='newsletter-embed-item-icon'>" + content_icon + "</td>";
		}
 	}

 	content += "<td class='newsletter-embed-item-title'>";
 	content += "<table><tr><td><h3><a href='" + data.url + "'>" + data.title + "</a></h3></td></tr><tr><td class='newsletter-embed-item-description'>" + content_description + "</td></tr></table>";
 	content += "</td>";

 	if (content_icon) {
		if (icon_option === "right") {
			content += "<td class='newsletter-embed-item-icon'>" + content_icon + "</td>"; 
		}
 	}
 	
 	content += "</tr></table>";
	
	$preview.html(content);
	$.colorbox.resize();
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
