define(function(require) {
	
	var $ = require('jquery');
	
	$(document).on('change', '#newsletter-edit-template-select input[type="radio"]', function(event) {
		event.stopPropagation();
		
		if ($(this).val() == 'custom') {
			$('.newsletter-edit-template-custom').removeClass('hidden');
			$('.newsletter-edit-template-custom').find('input, textarea').prop('disabled', false);
			$('#newsletter-edit-template-save-as').closest('div.elgg-field').removeClass('hidden');
		} else {
			$('.newsletter-edit-template-custom').addClass('hidden');
			$('.newsletter-edit-template-custom').find('input, textarea').prop('disabled', true);
			$('#newsletter-edit-template-save-as').closest('div.elgg-field').addClass('hidden');
		}
	});
	
	$(document).on('click', '#newsletter-edit-template-save-as', function() {
		
		var $form = $('#newsletter-form-template');
		
		$form.find('input[name="name"]').prop('required', true);
		
		if (typeof $form.get(0).reportValidity === 'function') {
			if (!$form.get(0).reportValidity()) {
				$form.find('input[name="name"]').prop('required', false);
				
				return false;
			}
		}
		
		$form.find('input[name="name"]').prop('required', false);
		
		var Ajax = require('elgg/Ajax');
		
		var ajax = new Ajax();
		ajax.action('newsletter/template/edit', {
			data: {
				newsletter_guid: $form.find('input[name="guid"]').val(),
				html: $form.find('textarea[name="html"]').val(),
				css: $form.find('textarea[name="css"]').val(),
				name: $form.find('input[name="name"]').val()
			},
			success: function() {
				document.location.href = document.location.href;
			}
		});
	});
});
