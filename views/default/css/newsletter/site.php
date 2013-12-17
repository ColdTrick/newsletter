<?php
// .newsletter-sorting .ui-sortable-helper .newsletter-sub-section {
// 	display: none;
// }

?>
.newsletter-section {
	padding-bottom: 2px;
}

.newsletter-section > table {
	width: 100%;
}

.newsletter-sub-section {
	margin-left: 30px;
}

.newsletter-sorting-placeholder {
	border: 1px dashed #CCC;
	margin-bottom: 2px;
}

.newsletter-section-drag,
.newsletter-section-actions {
	width: 1px;
	text-align: center;
	padding: 5px;
	white-space: nowrap;
}

.newsletter-actions-edit,
.newsletter-section-actions .elgg-icon {
	cursor: pointer;
}

#newsletter-recipients-wrapper .elgg-icon-delete-alt {
	display: none;
}

#newsletter-recipients-wrapper .newsletter-recipient-result {
	border: 1px solid transparent;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}

#newsletter-recipients-wrapper .newsletter-recipient-result:hover {
	border-color: #4690D6;
	background: #E4ECF5;
}

#newsletter-recipients-wrapper .newsletter-recipient-result:hover .elgg-icon-delete-alt {
	display: inline-block;
}

#newsletter-recipients-wrapper .elgg-module,
#newsletter-recipients-wrapper.elgg-module {
	margin-bottom: 10px;
}

.newsletter-recipient-autocomplete-group span,
.newsletter-recipient-autocomplete-email span {
	color: black;
}

#newsletter-recipients-users > .elgg-body,
#newsletter-recipients-groups > .elgg-body,
#newsletter-recipients-emails > .elgg-body {
	padding-left: 5px;
	border-left: 1px solid #CCCCCC;
}

#newsletter-settings-form .newsletter-settings-small {
	width: 25px;
	text-align: center;
}
