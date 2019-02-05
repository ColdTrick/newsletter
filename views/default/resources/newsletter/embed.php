<?php

use Elgg\BadRequestException;

$guid = (int) get_input('guid');
elgg_entity_gatekeeper($guid, 'object', Newsletter::SUBTYPE);

if (!newsletter_embed_available()) {
	throw new BadRequestException();
}

echo elgg_view('newsletter/embed', ['entity' => get_entity($guid)]);
