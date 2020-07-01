define(function(require) {
	var $ = require('jquery');
	var elgg = require('elgg');
	var Ajax = require('elgg/Ajax');
	var lightbox = require('elgg/lightbox');

	var init = function() {
	
		$(document).on('click', '#newsletter-embed-list li', function(event) {
			embed_format(this);
			event.preventDefault();
		});
	
		$(document).on('click', '#newsletter-embed-pagination a', function(event) {
			event.preventDefault();
			
			var ajax = new Ajax();
			var $link = $(this);
			ajax.path($link.attr('href'), {
				success: function(result) {
					$link.parents('#newsletter-embed-wrapper').replaceWith(result);
					$(window).trigger('resize.lightbox');
				}
			});
		});
	
		$(document).on('submit', '#newsletter-embed-search', function(event) {
			event.preventDefault();
			
			var ajax = new Ajax();
			var $form = $(this);
			
			ajax.path($form.attr('action'), {
				data: ajax.objectify($form),
				success: function(result) {
					$form.parents('#newsletter-embed-wrapper').replaceWith(result);
					$(window).trigger('resize.lightbox');
				}
			});
		});
	
		$(document).on('change', '#newsletter-embed-format-description, #newsletter-embed-format-icon', function() {
			embed_format_preview();
		});
		
		$(document).on('click', '.newsletter-embed-format', function(event) {
			event.preventDefault();
			
			embed($("#newsletter-embed-format-preview").html());
		});
	}
	
	var embed_format = function(elem) {
		var data = $(elem).find("> div").data();
		if (!data) {
			return false;
		}
		
		$("#newsletter-embed-format-icon").parent().hide();
		if (data.iconUrl) {
			$("#newsletter-embed-format-icon").parent().show();
		}
			
		$("#newsletter-embed-search, #newsletter-embed-list, #newsletter-embed-pagination, #newsletter-embed-format").toggleClass("hidden");
		
		$("#newsletter-embed-format-preview").data(data);
	
		embed_format_preview();
	}
	
	var embed_format_preview = function() {
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
	 	
	 	content += "<table class='newsletter-embed-item newsletter-widget'><tr>";
	
	 	if (content_icon) {
			if (icon_option === "left") {
				content += "<td><div class='newsletter-embed-item-icon'>" + content_icon + "</div></td>";
			}
	 	}
	
	 	content += "<td>";
	 	content += "<table><tr><td><h3 class='newsletter-embed-item-title'><a href='" + data.url + "'>" + data.title + "</a></h3></td></tr><tr><td><div class='newsletter-embed-item-description'>" + content_description + "</div></td></tr></table>";
	 	content += "</td>";
	
	 	if (content_icon) {
			if (icon_option === "right") {
				content += "<td><div class='newsletter-embed-item-icon'>" + content_icon + "</div></td>";
			}
	 	}
	 	
	 	content += "</tr></table>";
		
		$preview.html(content);
		$(window).trigger('resize.lightbox');
	}
	
	var embed = function(content) {
	
		var textAreaId = $(".elgg-form-newsletter-edit-content textarea").attr("id");
		var textArea = $("#" + textAreaId);
		
		textArea.val(textArea.val() + content);
		textArea.focus();
	
		lightbox.close();
	}
	
	//register init hook
	elgg.register_hook_handler("init", "system", init);
});

