<?php
/**
 * Show the subscribe form in a widget
 */

$widget = elgg_extract('entity', $vars);

echo elgg_view_form('newsletter/subscribe', [], ['entity' => $widget->getOwnerEntity()]);
