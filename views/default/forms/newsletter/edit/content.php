<?php

$entity = $vars["entity"];

$id = "newsletter-edit-content-" . $entity->getGUID();

echo elgg_view("tinymce/init");

echo elgg_view("output/text", array("value" => elgg_echo("newsletter:edit:content:description")));

echo "<div class='mvm'>";

echo elgg_view_menu('longtext', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
	'id' => $id,
));

echo elgg_view("input/plaintext", array("name" => "content", "value" => $entity->content, "id" => $id, "class" => "newsletter-input-plaintext"));
echo "</div>";

echo elgg_view("newsletter/placeholders");

echo "<div class='elgg-foot mtm'>";
echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
echo elgg_view("input/submit", array("value" => elgg_echo("save")));
echo "</div>";

?>
<script type="text/javascript">
	elgg.provide('elgg.newsletter');
	
	elgg.newsletter.tinymce = function() {
		
		$('.newsletter-input-plaintext').parents('form').submit(function() {
			tinyMCE.triggerSave();
		});

		tinyMCE.init({
			mode : "specific_textareas",
			editor_selector : "newsletter-input-plaintext",
			theme : "advanced",
			language : "<?php echo tinymce_get_site_language(); ?>",
			plugins : "lists,spellchecker,autosave,inlinepopups,embed,media,paste,table,style,advimage,advlink,contextmenu,fullscreen,visualchars,nonbreaking,xhtmlxtras",
			relative_urls : false,
			remove_script_host : false,
			document_base_url : elgg.config.wwwroot,
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,|,forecolor,formatselect,fontselect,fontsizeselect,|,justifyleft,justifycenter,justifyright,justifyfull",
			theme_advanced_buttons2 : "pastetext,pasteword,|,undo,redo,outdent,indent,blockquote,|,link,unlink,image,code,fullscreen",
			theme_advanced_buttons3 : "tablecontrols",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			theme_advanced_path : true,
			width : "100%",
			extended_valid_elements : "*",
			setup : function(ed) {
				//show the number of words
				ed.onLoadContent.add(function(ed, o) {
					var strip = (tinyMCE.activeEditor.getContent()).replace(/(&lt;([^&gt;]+)&gt;)/ig,"");
					var text = elgg.echo('tinymce:word_count') + strip.split(' ').length + ' ';
					tinymce.DOM.setHTML(tinymce.DOM.get(tinyMCE.activeEditor.id + '_path_row'), text);
				});

				ed.onKeyUp.add(function(ed, e) {
					var strip = (tinyMCE.activeEditor.getContent()).replace(/(&lt;([^&gt;]+)&gt;)/ig,"");
					var text = elgg.echo('tinymce:word_count') + strip.split(' ').length + ' ';
					tinymce.DOM.setHTML(tinymce.DOM.get(tinyMCE.activeEditor.id + '_path_row'), text);
				});

				ed.onInit.add(function(ed) {
					// prevent Firefox from dragging/dropping files into editor
					if (tinymce.isGecko) {
						tinymce.dom.Event.add(ed.getBody().parentNode, "drop", function(e) {
							if (e.dataTransfer.files.length > 0) {
								e.preventDefault();
							}
						});
					}
				});

			},
			content_css: elgg.config.wwwroot + 'mod/tinymce/css/elgg_tinymce.css'
		});
		
	}

	elgg.register_hook_handler('init', 'system', elgg.newsletter.tinymce);
	
</script>