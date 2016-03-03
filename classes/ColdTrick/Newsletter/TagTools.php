<?php

namespace ColdTrick\Newsletter;

class TagTools {
	
	/**
	 * Modify the tag_tools type/subtypes for notifications
	 *
	 * @param string $hook_name    the name of the hook
	 * @param string $entity_type  the type of the hook
	 * @param array  $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return array|void
	 */
	public static function notificationTypeSubtype($hook_name, $entity_type, $return_value, $params) {
	
		$object_subtypes = elgg_extract('object', $return_value);
		if (empty($object_subtypes) || !is_array($object_subtypes)) {
			return;
		}
	
		$key = array_search(\Newsletter::SUBTYPE, $object_subtypes);
		if ($key === false) {
			return;
		}
	
		unset($object_subtypes[$key]);
		$return_value['object'] = array_values($object_subtypes);
	
		return $return_value;
	}
}