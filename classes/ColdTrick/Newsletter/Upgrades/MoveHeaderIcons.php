<?php

namespace ColdTrick\Newsletter\Upgrades;

use Elgg\Upgrade\AsynchronousUpgrade;
use Elgg\Upgrade\Result;

class MoveHeaderIcons extends AsynchronousUpgrade {

	/**
	 * {@inheritdoc}
	 */
	public function getVersion(): int {
		return 2023040400;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function needsIncrementOffset(): bool {
		return false;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function shouldBeSkipped(): bool {
		return empty($this->countItems());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function countItems(): int {
		return elgg_count_entities($this->getOptions());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function run(Result $result, $offset): Result {
		
		/**
		 * Old icons had a custom size. Registering it here, so it will be removed correctly.
		 *
		 * @param \Elgg\Event $event 'entity:icon:sizes', 'object'
		 *
		 * @return array
		 */
		$register_old_size = function (\Elgg\Event $event) {
			if ($event->getParam('entity_subtype') !== 'newsletter') {
				return;
			}
			
			$returnvalue = $event->getValue();
			
			$returnvalue['newsletter_header'] = [
				'w' => 600,
				'h' => 240,
				'square' => false,
				'upscale' => true,
				'crop' => true,
			];
			
			return $returnvalue;
		};
		
		elgg_register_event_handler('entity:icon:sizes', 'object', $register_old_size);
		
		$newsletters = elgg_get_entities($this->getOptions(['offset' => $offset]));
		/* @var $newsletter \Newsletter */
		foreach ($newsletters as $newsletter) {
			$old_icon = $newsletter->getIcon('master', 'icon');
			if ($old_icon->exists()) {
				$coords = [
					'x1' => $newsletter->x1,
					'y1' => $newsletter->y1,
					'x2' => $newsletter->x2,
					'y2' => $newsletter->y2,
				];
				
				$newsletter->saveIconFromElggFile($old_icon, 'header', $coords);
			}
			
			$newsletter->deleteIcon('icon');
			
			$result->addSuccesses();
		}
		
		elgg_unregister_event_handler('entity:icon:sizes', 'object', $register_old_size);
		
		return $result;
	}
	
	/**
	 * Get options for elgg_get_entities
	 *
	 * @param array $options additional options
	 *
	 * @return array
	 */
	protected function getOptions(array $options = []) {
		$defaults = [
			'type' => 'object',
			'subtype' => 'newsletter',
			'limit' => 50,
			'batch' => true,
			'metadata_name' => 'icontime',
		];
		
		return array_merge($defaults, $options);
	}
}
