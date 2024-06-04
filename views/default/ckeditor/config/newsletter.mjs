import 'jquery';
import elgg from 'elgg';
import hooks from 'elgg/hooks';
import i18n from 'elgg/i18n';
import lightbox from 'elgg/lightbox';
import Ajax from 'elgg/Ajax';
import default_config from 'ckeditor/config/default';

var active_editor;

function embed_format(elem) {
	var data = $(elem).find('> div').data();
	if (!data) {
		return false;
	}
	
	$('#newsletter-embed-format-icon').closest('.elgg-field').hide();
	if (data.iconUrl) {
		$('#newsletter-embed-format-icon').closest('.elgg-field').show();
	}
	
	$('#newsletter-embed-search, #newsletter-embed-list, #newsletter-embed-pagination, #newsletter-embed-format').toggleClass('hidden');
	
	$('#newsletter-embed-format-preview').data(data);

	embed_format_preview();
}

async function embed_format_preview() {
	var $preview = $('#newsletter-embed-format-preview');
	var data = $preview.data();
	var content = '';
	var content_description = '';

	var description_option = $('#newsletter-embed-format-description').val();
	var icon_option = $('#newsletter-embed-format-icon').is(':checked');
	
	if (description_option === 'full') {
		content_description += data.description;
	} else if (description_option === 'excerpt') {
		content_description += data.excerpt;
		content_description += "<p class='newsletter-read-more'><a href='" + data.url + "'>" + i18n.echo('newsletter:embed:read_more') + " ></a></p>";
	}
	
	content += "<table class='newsletter-item' style='width: 100%'>";
	
	if (data.iconUrl && icon_option) {
		content += "<tr><td><img src='" + data.iconUrl + "' class='image_resized' style='width: 600px'/></td></tr>";
	}
	
	content += "<tr><td><h3>";
	content += "<a href='" + data.url + "'>" + data.title + "</a>";
	content += "</h3></td></tr>";
	content += "<tr><td>" + content_description + "</td></tr>";
	content += "</table>";
	
	$preview.html(content);
	
	// a little sleep helps setting the correct size
	await new Promise(r => setTimeout(r, 100));
	
	lightbox.resize();
}

function embed(content) {
	var htmlDP = active_editor.data.processor;
	var viewFragment = htmlDP.toView(content);
	var modelFragment = active_editor.data.toModel(viewFragment);
	
	active_editor.model.insertContent(modelFragment);

	lightbox.close();
}

$(document).on('click', '#newsletter-embed-list li', function(event) {
	event.preventDefault();
	
	embed_format(this);
});

$(document).on('click', '#newsletter-embed-pagination a', function(event) {
	event.preventDefault();
	
	var ajax = new Ajax();
	var $link = $(this);
	ajax.path($link.attr('href'), {
		success: function(result) {
			$link.parents('#newsletter-embed-wrapper').replaceWith(result);
			lightbox.resize();
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
			lightbox.resize();
		}
	});
});

$(document).on('click', '#newsletter-embed-add-blank', function(event) {
	event.preventDefault();
	
	var content = '';
	
	content += "<table class='newsletter-item' style='width: 100%'>";
	content += "<tr><td>" + i18n.echo('newsletter:embed:blank:img') + "</td></tr>";
	content += "<tr><td><h3>";
	content += "<a href='#'>" + i18n.echo('newsletter:embed:blank:title') + "</a>";
	content += "</h3></td></tr>";
	content += "<tr><td>";
	content += i18n.echo('newsletter:embed:blank:description');
	content += "<p class='newsletter-read-more'><a href='#'>" + i18n.echo('newsletter:embed:read_more') + " ></a></p>";
	content += "</td></tr>";
	content += "</table>";
	
	embed(content);
});

$(document).on('change', '#newsletter-embed-format-description, #newsletter-embed-format-icon', function() {
	embed_format_preview();
});

$(document).on('click', '.newsletter-embed-format', function(event) {
	event.preventDefault();
	
	embed($("#newsletter-embed-format-preview").html());
});

// Using a hook to find the newsletter guid before adding the button to the toolbar
hooks.register('config', 'ckeditor', function(type, action, params, result) {
	if (params.editor !== 'newsletter') {
		return;
	}
	
	var guid = $(params.selector).closest('form').find('input[name="guid"]').val();
	if (!guid) {
		return;
	}

	result.simpleButton = [{
		name: 'newsletter',
		label: i18n.echo('newsletter:editor:button'),
		icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M304 192v32c0 6.6-5.4 12-12 12h-56v56c0 6.6-5.4 12-12 12h-32c-6.6 0-12-5.4-12-12v-56h-56c-6.6 0-12-5.4-12-12v-32c0-6.6 5.4-12 12-12h56v-56c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v56h56c6.6 0 12 5.4 12 12zm201 284.7L476.7 505c-9.4 9.4-24.6 9.4-33.9 0L343 405.3c-4.5-4.5-7-10.6-7-17V372c-35.3 27.6-79.7 44-128 44C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208c0 48.3-16.4 92.7-44 128h16.3c6.4 0 12.5 2.5 17 7l99.7 99.7c9.3 9.4 9.3 24.6 0 34zM344 208c0-75.2-60.8-136-136-136S72 132.8 72 208s60.8 136 136 136 136-60.8 136-136z"/></svg>',
		syncDisabledState: false,
		onClick: (buttonView) => {
			active_editor = $(buttonView.element).closest('.ck-editor').find('.ck-editor__editable')[0].ckeditorInstance;
			
			lightbox.open({
				href: elgg.normalize_url('newsletter/embed/' + guid),
				ajaxLoadWithDependencies: true
			});
		}
	}];
	
	result.toolbar.items.push('newsletter');
	
	// the following allows the custom styled elements
	result.htmlSupport.allow.push({
		name: 'table',
		classes: ['newsletter-item']
	});
	result.htmlSupport.allow.push({
		name: 'p',
		classes: ['newsletter-read-more']
	});
	
	return result;
});

export default default_config;
