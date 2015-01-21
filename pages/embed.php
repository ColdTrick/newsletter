<?php

$guid = (int) get_input("guid");
elgg_entity_gatekeeper($guid, "object", Newsletter::SUBTYPE);

if (!newsletter_embed_available()) {
	forward();
}

$entity = get_entity($guid);

echo elgg_view("newsletter/embed", array("entity" => $entity));
