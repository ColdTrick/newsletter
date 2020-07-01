CKEDITOR.plugins.add('newsletter', {
	requires: 'widget',
	
    init: function (editor) {
    	editor.widgets.add('newsletter', {
    		inline: false,
    		
    		upcast: function (element) {
    			// Defines which elements will become widgets.
    			if (element.hasClass('newsletter-widget')) {
    				return true;
    			}
    		},
    		
    		editables: {
    			header: {
    				selector: 'h3.newsletter-embed-item-title'
    			},
    			content: {
    				selector: 'div.newsletter-embed-item-description'
    			},
    			icon: {
    				selector: 'div.newsletter-embed-item-icon'
    			}
    		}
    	});
    }
});
