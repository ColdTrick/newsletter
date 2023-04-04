<?php

$guid = (int) get_input('guid');
elgg_entity_gatekeeper($guid, 'object', \Newsletter::SUBTYPE, true);

echo elgg_view('newsletter/embed', ['entity' => get_entity($guid)]);
