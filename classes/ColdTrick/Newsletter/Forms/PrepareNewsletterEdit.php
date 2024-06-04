<?php

namespace ColdTrick\Newsletter\Forms;

use Elgg\Values;

/**
 * Newsletter edit form helper class
 */
class PrepareNewsletterEdit {
	
	protected ?\Newsletter $entity = null;
	
	protected ?int $container_guid = null;
	
	/**
	 * Prepare form vars
	 *
	 * @param \Elgg\Event $event 'form:prepare:fields', 'newsletter/edit[/subpage]'
	 *
	 * @return null|array
	 */
	public function __invoke(\Elgg\Event $event): ?array {
		$vars = $event->getValue();
		
		$entity = elgg_extract('entity', $vars);
		if ($entity instanceof \Newsletter) {
			$this->entity = $entity;
		}
		
		$container_guid = elgg_extract('container_guid', $vars);
		if (!empty($container_guid)) {
			$this->container_guid = (int) $container_guid;
		}
		
		switch ($event->getType()) {
			case 'newsletter/edit/template':
				return $vars;
			case 'newsletter/edit/content':
				return $this->prepareContent($vars);
			case 'newsletter/edit/recipients':
				return $this->prepareRecipients($vars);
			case 'newsletter/edit/schedule':
				return $this->prepareSchedule($vars);
		}
		
		return $this->prepareBasic($vars);
	}
	
	/**
	 * Prepare basic entity edit vars
	 *
	 * @param array $vars current form body_vars
	 *
	 * @return array
	 */
	protected function prepareBasic(array $vars): array {
		$defaults = [
			'title' => '',
			'description' => '',
			'subject' => '',
			'from' => '',
			'access_id' => elgg_get_default_access(null, [
				'entity_type' => 'object',
				'entity_subtype' => \Newsletter::SUBTYPE,
				'container_guid' => $this->container_guid,
				'purpose' => 'read',
			]),
			'tags' => [],
			'container_guid' => $this->container_guid,
		];
		
		if ($this->entity instanceof \Newsletter) {
			foreach ($defaults as $name => $value) {
				$defaults[$name] = $this->entity->$name;
			}
		}
		
		return array_merge($defaults, $vars);
	}
	
	/**
	 * Prepare content step
	 *
	 * @param array $vars current form body_vars
	 *
	 * @return array
	 */
	protected function prepareContent(array $vars): array {
		$defaults = [
			'content' => $this->entity->content,
		];
		
		return array_merge($defaults, $vars);
	}
	
	/**
	 * Prepare recipients step
	 *
	 * @param array $vars current form body_vars
	 *
	 * @return array
	 */
	protected function prepareRecipients(array $vars): array {
		$recipients = $this->entity->getRecipients();
		
		$defaults = [
			'user_guids' => elgg_extract('user_guids', $recipients),
			'group_guids' => elgg_extract('group_guids', $recipients),
			'emails' => elgg_extract('emails', $recipients),
			'subscribers' => elgg_extract('subscribers', $recipients),
			'members' => elgg_extract('members', $recipients),
		];
		
		return array_merge($defaults, $vars);
	}
	
	/**
	 * Prepare schedule step
	 *
	 * @param array $vars current form body_vars
	 *
	 * @return array
	 */
	protected function prepareSchedule(array $vars): array {
		if ($this->entity->scheduled) {
			$date = Values::normalizeTime($this->entity->scheduled);
		} else {
			$date = Values::normalizeTime(time());
			$date->modify('+1 day');
		}
		
		$defaults = [
			'scheduled' => $date->getTimestamp(),
			'show_in_archive' => isset($this->entity->show_in_archive) ? (int) $this->entity->show_in_archive : 1,
			'status_notification' => isset($this->entity->status_notification) ? $this->entity->status_notification : elgg_get_logged_in_user_entity()->email,
		];
		
		return array_merge($defaults, $vars);
	}
}
