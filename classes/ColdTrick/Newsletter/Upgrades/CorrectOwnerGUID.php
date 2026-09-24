<?php

namespace ColdTrick\Newsletter\Upgrades;

use Elgg\Database\QueryBuilder;
use Elgg\Upgrade\Result;
use Elgg\Upgrade\SystemUpgrade;

class CorrectOwnerGUID extends SystemUpgrade {
	
	/**
	 * {@inheritdoc}
	 */
	public function getVersion(): int {
		return 2026092401;
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
	public function needsIncrementOffset(): bool {
		return false;
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
		/** @var \ElggBatch $entities */
		$entities = elgg_get_entities($this->getOptions(['offset' => $offset]));
		
		/** @var \Newsletter $entity */
		foreach ($entities as $entity) {
			$entity->owner_guid = $entity->container_guid;
			
			if (!$entity->save()) {
				$entities->reportFailure();
				$result->addFailures();
			} else {
				$result->addSuccesses();
			}
		}
		
		return $result;
	}
	
	/**
	 * Get the selection options
	 *
	 * @param array $options additional options
	 *
	 * @return array
	 * @see elgg_get_entities()
	 */
	protected function getOptions(array $options = []): array {
		$defaults = [
			'type' => 'object',
			'subtype' => \Newsletter::SUBTYPE,
			'limit' => 50,
			'wheres' => [
				function (QueryBuilder $qb, $main_alias) {
					return $qb->compare("{$main_alias}.owner_guid", '!=', "{$main_alias}.container_guid");
				},
			],
			'batch' => true,
			'batch_inc_offset' => $this->needsIncrementOffset(),
		];
		
		return array_merge($defaults, $options);
	}
}
