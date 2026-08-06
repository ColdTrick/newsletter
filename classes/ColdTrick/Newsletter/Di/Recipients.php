<?php

namespace ColdTrick\Newsletter\Di;

use Elgg\Database\QueryBuilder;
use Elgg\Database\RelationshipsTable;
use Elgg\Traits\Di\ServiceFacade;

/**
 * Find recipients for a newsletter
 */
class Recipients {
	
	use ServiceFacade;
	
	protected ?\Newsletter $entity = null;
	
	protected ?array $base_options = null;
	
	/**
	 * {@inheritdoc}
	 */
	public static function name(): string {
		return 'newsletter.recipients';
	}
	
	/**
	 * Get all the relevant recipients of a given newsletter
	 *
	 * @param \Newsletter $entity newsletter
	 *
	 * @return array
	 */
	public function getFilteredRecipients(\Newsletter $entity): array {
		$this->reset();
		
		$this->entity = $entity;
		
		try {
			$result = elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity) {
				$recipients = $entity->getRecipients();
				if (empty($recipients)) {
					return [];
				}
				
				$site = elgg_get_site_entity();
				$container = $entity->getContainerEntity();
				
				$filtered_recipients = [
					'users' => [],
					'emails' => [],
				];
				
				// basic set of user selection options
				$basic_user_options = $this->getBaseOptions();
				
				// recipients is an array consisting of:
				// - user_guids: 	individual users
				// - group_guids:	groups to send the content to
				// - emails:		individual email addresses
				// - subscribers:	(int) whether to add the subscribers of the container
				// - members:		(int) whether to aad the member of the container
				$user_guids = elgg_extract('user_guids', $recipients);
				if (!empty($user_guids)) {
					if (!is_array($user_guids)) {
						$user_guids = [$user_guids];
					}
					
					$temp_recipients = $this->getUserGuids($user_guids);
					
					$filtered_recipients['users'] += $temp_recipients;
				}
				
				$group_guids = elgg_extract('group_guids', $recipients);
				if (!empty($group_guids)) {
					if (!is_array($group_guids)) {
						$group_guids = [$group_guids];
					}
					
					$temp_recipients = $this->getGroupMembers($group_guids);
					
					$filtered_recipients['users'] += $temp_recipients;
				}
				
				$subscribers = elgg_extract('subscribers', $recipients);
				if (!empty($subscribers)) {
					$temp_recipients = $this->getSubscribers($container);
					
					$filtered_recipients['users'] += $temp_recipients['users'];
					$filtered_recipients['emails'] = array_merge($filtered_recipients['emails'], $temp_recipients['emails']);
				}
				
				$members = elgg_extract('members', $recipients);
				if (!empty($members)) {
					$temp_recipients = $this->getMembers($container);
					
					$filtered_recipients['users'] += $temp_recipients;
				}
				
				$emails = elgg_extract('emails', $recipients);
				if (!empty($emails)) {
					if (!is_array($emails)) {
						$emails = [$emails];
					}
					
					$temp_recipients = $this->filterEmails($emails, $container);
					
					if (!empty($temp_recipients)) {
						$filtered_recipients['emails'] = array_merge($filtered_recipients['emails'], $temp_recipients);
					}
				}
				
				return $filtered_recipients;
			});
		} catch (\Throwable $t) {
			// something happened
			$result = [];
		}
		
		$this->reset();
		
		return $result;
	}
	
	/**
	 * Get all the people that subscribed to the newsletter of this container
	 *
	 * @param \ElggEntity $container Which container
	 * @param bool        $count     Return just a count, not the actual subscribers
	 *
	 * @return false|int|array
	 */
	public function getSubscribers(\ElggEntity $container, bool $count = false): false|int|array {
		if (!$container instanceof \ElggSite && !$container instanceof \ElggGroup) {
			return false;
		}
		
		// get the subscribers
		if (!$count) {
			$result = [
				'users' => [],
				'emails' => [],
			];
			
			// get all subscribed community members
			$user_emails = elgg_get_metadata([
				'type' => 'user',
				'metadata_names' => ['email'],
				'limit' => false,
				'batch' => true,
				'relationship' => \NewsletterSubscription::SUBSCRIPTION,
				'relationship_guid' => $container->guid,
				'inverse_relationship' => true,
			]);
			/** @var \ElggMetadata $user_email */
			foreach ($user_emails as $user_email) {
				$result['users'][$user_email->entity_guid] = $user_email->value;
			}
			
			// check the email subscriptions
			$result['emails'] = elgg_get_entities([
				'type' => 'object',
				'subtype' => \NewsletterSubscription::SUBTYPE,
				'selects' => [
					function (QueryBuilder $qb, $main_alias) {
						$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'title');
						
						return "{$metadata}.value AS title";
					},
				],
				'limit' => false,
				'relationship' => \NewsletterSubscription::SUBSCRIPTION,
				'relationship_guid' => $container->guid,
				'inverse_relationship' => true,
				'callback' => function($row) {
					return $row->title;
				},
			]);
		} else {
			// get all subscribed community members
			$result = elgg_count_entities([
				'type' => 'user',
				'relationship' => \NewsletterSubscription::SUBSCRIPTION,
				'relationship_guid' => $container->guid,
				'inverse_relationship' => true,
			]);
			
			// check the email subscriptions
			$result += elgg_count_entities([
				'type' => 'object',
				'subtype' => \NewsletterSubscription::SUBTYPE,
				'relationship' => \NewsletterSubscription::SUBSCRIPTION,
				'relationship_guid' => $container->guid,
				'inverse_relationship' => true,
			]);
		}
		
		return $result;
	}
	
	/**
	 * Reset internal caches
	 *
	 * @return void
	 */
	protected function reset(): void {
		$this->entity = null;
		$this->base_options = null;
	}
	
	/**
	 * Get the base options for recipient selection
	 *
	 * @return array
	 * @throws \Elgg\Exceptions\RuntimeException
	 * @see elgg_get_entities()
	 */
	protected function getBaseOptions(): array {
		if (!isset($this->entity)) {
			throw new \Elgg\Exceptions\RuntimeException();
		}
		
		if (isset($this->base_options)) {
			return $this->base_options;
		}
		
		$site = elgg_get_site_entity();
		$container = $this->entity->getContainerEntity();
		
		$this->base_options = [
			'type' => 'user',
			'limit' => false,
			'batch' => true,
			'selects' => [
				function (QueryBuilder $qb, $main_alias) {
					$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'email');
					
					return "{$metadata}.value AS email";
				},
			],
			'callback' => false,
			'wheres' => [],
			'metadata_name_value_pairs' => [],
		];
		
		// include banned users?
		if ((bool) !elgg_get_plugin_setting('include_banned_users', 'newsletter')) {
			$this->base_options['metadata_name_value_pairs'][] = [
				'name' => 'banned',
				'value' => 'no',
			];
		}
		
		// include users without settings
		if (elgg_get_plugin_setting('include_existing_users', 'newsletter') === 'yes') {
			// yes, so exclude blocked
			$this->base_options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($site) {
				// general blacklist
				$blocked = $qb->subquery(RelationshipsTable::TABLE_NAME);
				$blocked->select('guid_one')
					->where($qb->compare('relationship', '=', \NewsletterSubscription::GENERAL_BLACKLIST, ELGG_VALUE_STRING))
					->andWhere($qb->compare('guid_two', '=', $site->guid, ELGG_VALUE_GUID));
				
				return $qb->compare("{$main_alias}.guid", 'not in', $blocked->getSQL());
			};
			$this->base_options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($container) {
				// blacklist / unsubscribed
				$blocked = $qb->subquery(RelationshipsTable::TABLE_NAME);
				$blocked->select('guid_one')
					->where($qb->compare('relationship', '=', \NewsletterSubscription::BLACKLIST, ELGG_VALUE_STRING))
					->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
				
				return $qb->compare("{$main_alias}.guid", 'not in', $blocked->getSQL());
			};
		} else {
			// no, so subscription is required
			$this->base_options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($container) {
				$subbed = $qb->subquery(RelationshipsTable::TABLE_NAME);
				$subbed->select('guid_one')
					->where($qb->compare('relationship', '=', \NewsletterSubscription::SUBSCRIPTION, ELGG_VALUE_STRING))
					->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
				
				return $qb->compare("{$main_alias}.guid", 'in', $subbed->getSQL());
			};
		}
		
		return $this->base_options;
	}
	
	/**
	 * Get existing recipients from user GUIDs
	 *
	 * @param array $user_guids unfiltered user GUIDs
	 *
	 * @return array
	 */
	protected function getUserGuids(array $user_guids): array {
		// convert to a format we can use
		$options = $this->getBaseOptions();
		$options['wheres'][] = function(QueryBuilder $qb, $main_alias) use ($user_guids) {
			return $qb->compare("{$main_alias}.guid", 'in', $user_guids, ELGG_VALUE_GUID);
		};
		
		$result = [];
		
		/** @var \ElggBatch $users */
		$users = elgg_get_entities($options);
		/** @var \stdClass $row */
		foreach ($users as $row) {
			$result[(int) $row->guid] = $row->email;
		}
		
		return $result;
	}
	
	/**
	 * Get the members of the given groups
	 *
	 * @param array $group_guids group GUIDSs
	 *
	 * @return array
	 */
	protected function getGroupMembers(array $group_guids): array {
		$options = $this->getBaseOptions();
		
		$options['relationship_guid'] = $group_guids;
		$options['relationship'] = 'member';
		$options['inverse_relationship'] = true;
		
		$result = [];
		
		/** @var \ElggBatch $users */
		$users = elgg_get_entities($options);
		/** @var \stdClass $row */
		foreach ($users as $row) {
			$result[(int) $row->guid] = $row->email;
		}
		
		return $result;
	}
	
	/**
	 * Get the members the site or a group
	 *
	 * @param \ElggEntity|null $container the container to check for
	 *
	 * @return array
	 */
	protected function getMembers(?\ElggEntity $container = null): array {
		$options = $this->getBaseOptions();
		
		if ($container instanceof \ElggGroup) {
			$options['relationship'] = 'member';
			$options['relationship_guid'] = $container->guid;
			$options['inverse_relationship'] = true;
		}
		
		$result = [];
		
		/** @var \ElggBatch $users */
		$users = elgg_get_entities($options);
		/** @var \stdClass $row */
		foreach ($users as $row) {
			$result[(int) $row->guid] = $row->email;
		}
		
		return $result;
	}
	
	/**
	 * Filter email addresses with blocked or banned users
	 *
	 * @param array            $emails    original emails
	 * @param \ElggEntity|null $container newsletter container
	 *
	 * @return array
	 */
	protected function filterEmails(array $emails, ?\ElggEntity $container = null): array {
		$site = elgg_get_site_entity();
		
		// filter out blocked users
		$options = [
			'type' => 'user',
			'limit' => false,
			'batch' => true,
			'selects' => [
				function (QueryBuilder $qb, $main_alias) {
					$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'email');
					
					return "{$metadata}.value AS email";
				},
			],
			'metadata_name_value_pairs' => [
				[
					'name' => 'email',
					'value' => $emails,
					'case_sensitive' => false,
				],
			],
			'wheres' => [
				function (QueryBuilder $qb, $main_alias) use ($site, $container) {
					$wheres = [];
					
					// general blacklist
					$general = $qb->subquery(RelationshipsTable::TABLE_NAME);
					$general->select('guid_one')
						->where($qb->compare('relationship', '=', \NewsletterSubscription::GENERAL_BLACKLIST, ELGG_VALUE_STRING))
						->andWhere($qb->compare('guid_two', '=', $site->guid, ELGG_VALUE_GUID));
					
					$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $general->getSQL());
					
					// blacklist / unsubscribed
					$blacklist = $qb->subquery(RelationshipsTable::TABLE_NAME);
					$blacklist->select('guid_one')
						->where($qb->compare('relationship', '=', \NewsletterSubscription::BLACKLIST, ELGG_VALUE_STRING))
						->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
					
					$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $blacklist->getSQL());
					
					return $qb->merge($wheres, 'OR');
				},
			],
			'callback' => false,
		];
		
		// include banned users?
		if ((bool) !elgg_get_plugin_setting('include_banned_users', 'newsletter')) {
			$options['metadata_name_value_pairs'][] = [
				'name' => 'banned',
				'value' => 'no',
			];
		}
		
		$blocked_emails = [];
		
		/** @var \ElggBatch $users */
		$users = elgg_get_entities($options);
		/** @var \stdClass $row */
		foreach ($users as $row) {
			$blocked_emails[] = $row->email;
		}
		
		$emails = array_diff($emails, $blocked_emails);
		if (empty($emails)) {
			return [];
		}
		
		// filter out blocked email addresses
		$options = [
			'type' => 'object',
			'subtype' => \NewsletterSubscription::SUBTYPE,
			'limit' => false,
			'batch' => true,
			'selects' => [
				function (QueryBuilder $qb, $main_alias) {
					$metadata = $qb->joinMetadataTable($main_alias, 'guid', 'title');
					
					return "{$metadata}.value AS email";
				},
			],
			'metadata_name_value_pairs' => [
				[
					'name' => 'title',
					'value' => $emails,
					'case_sensitive' => false,
				],
			],
			'wheres' => [
				function (QueryBuilder $qb, $main_alias) use ($site, $container) {
					$wheres = [];
					
					// general blacklist
					$general = $qb->subquery(RelationshipsTable::TABLE_NAME);
					$general->select('guid_one')
						->where($qb->compare('relationship', '=', \NewsletterSubscription::GENERAL_BLACKLIST, ELGG_VALUE_STRING))
						->andWhere($qb->compare('guid_two', '=', $site->guid, ELGG_VALUE_GUID));
					
					$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $general->getSQL());
					
					// blacklist / unsubscribed
					$blacklist = $qb->subquery(RelationshipsTable::TABLE_NAME);
					$blacklist->select('guid_one')
						->where($qb->compare('relationship', '=', \NewsletterSubscription::BLACKLIST, ELGG_VALUE_STRING))
						->andWhere($qb->compare('guid_two', '=', $container->guid, ELGG_VALUE_GUID));
					
					$wheres[] = $qb->compare("{$main_alias}.guid", 'in', $blacklist->getSQL());
					
					return $qb->merge($wheres, 'OR');
				},
			],
			'callback' => false,
		];
		
		$blocked_emails = [];
		
		/** @var \ElggBatch $subscriptions */
		$subscriptions = elgg_get_entities($options);
		/** @var \stdClass $row */
		foreach ($subscriptions as $row) {
			$blocked_emails[] = $row->email;
		}
		
		return array_diff($emails, $blocked_emails);
	}
}
