<?php

namespace ColdTrick\Newsletter\Plugins;

/**
 * TagTools callbacks
 */
class TagTools {
	
	/**
	 * Modify the tag_tools type/subtypes for notifications
	 *
	 * @param \Elgg\Event $event 'notification_type_subtype', 'tag_tools'
	 *
	 * @return null|array
	 */
	public static function notificationTypeSubtype(\Elgg\Event $event): ?array {
		$return_value = $event->getValue();
		$object_subtypes = elgg_extract('object', $return_value);
		if (empty($object_subtypes) || !is_array($object_subtypes)) {
			return null;
		}
		
		$key = array_search(\Newsletter::SUBTYPE, $object_subtypes);
		if ($key === false) {
			return null;
		}
		
		unset($object_subtypes[$key]);
		$return_value['object'] = array_values($object_subtypes);
		
		return $return_value;
	}
}
