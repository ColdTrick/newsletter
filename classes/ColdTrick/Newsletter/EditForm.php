<?php

namespace ColdTrick\Newsletter;

use Elgg\Values;

class EditForm {
	
	/**
	 * @var \Newsletter
	 */
	protected $entity;
	
	/**
	 * @var int
	 */
	protected $container_guid;
	
	public function __construct(\Newsletter $entity = null, int $container_guid = 0) {
		
		if ($entity instanceof \Newsletter) {
			$this->entity = $entity;
		}
		
		if (!empty($container_guid)) {
			$this->container_guid = (int) $container_guid;
		}
	}
	
	/**
	 * Prepare form vars
	 *
	 * @param string $step which step of the proccess
	 *
	 * @return array
	 */
	public function __invoke(string $step = 'basic') {
		
		switch ($step) {
			case 'basic':
				return $this->prepareBasic();
			case 'template':
				return $this->prepareTemplate();
			case 'content':
				return $this->prepareContent();
			case 'recipients':
				return $this->prepareRecipients();
			case 'schedule':
				return $this->prepareSchedule();
		}
		
		return [];
	}
	
	/**
	 * Prepare basic entity edit vars
	 *
	 * @return array
	 */
	protected function prepareBasic() {
		
		$defaults = [
			'title' => '',
			'description' => '',
			'subject' => '',
			'from' => '',
			'access_id' => get_default_access(null, [
				'entity_type' => 'object',
				'entity_subtype' => \Newsletter::SUBTYPE,
				'container_guid' => $this->container_guid,
				'purpose' => 'read',
			]),
			'tags' => [],
			'container_guid' => $this->container_guid,
		];
		
		// edit?
		if ($this->entity instanceof \Newsletter) {
			foreach ($defaults as $name => $value) {
				$defaults[$name] = $this->entity->$name;
			}
			
			$defaults['entity'] = $this->entity;
		}
		
		// sticky form
		$sticky_values = elgg_get_sticky_values('newsletter/edit');
		if (!empty($sticky_values)) {
			foreach ($sticky_values as $name => $value) {
				$defaults[$name] = $value;
			}
			
			elgg_clear_sticky_form('newsletter/edit');
		}
		
		return $defaults;
	}
	
	/**
	 * Prepare template step
	 *
	 * @return array
	 */
	protected function prepareTemplate() {
		
		$defaults = [
			'entity' => $this->entity,
		];
		
		// sticky form
		$sticky_values = elgg_get_sticky_values('newsletter/edit/template');
		if (!empty($sticky_values)) {
			foreach ($sticky_values as $name => $value) {
				$defaults[$name] = $value;
			}
			
			elgg_clear_sticky_form('newsletter/edit/template');
		}
		
		return $defaults;
	}
	
	/**
	 * Prepare content step
	 *
	 * @return array
	 */
	protected function prepareContent() {
		
		$defaults = [
			'entity' => $this->entity,
			'content' => $this->entity->content,
		];
		
		// sticky form
		$sticky_values = elgg_get_sticky_values('newsletter/edit/content');
		if (!empty($sticky_values)) {
			foreach ($sticky_values as $name => $value) {
				$defaults[$name] = $value;
			}
			
			elgg_clear_sticky_form('newsletter/edit/content');
		}
		
		return $defaults;
	}
	
	/**
	 * Prepare recipients step
	 *
	 * @return array
	 */
	protected function prepareRecipients() {
		
		$entity = $this->entity;
		
		$recipients = $entity->getRecipients();
		
		$defaults = [
			'entity' => $this->entity,
			'user_guids' => elgg_extract('user_guids', $recipients),
			'group_guids' => elgg_extract('group_guids', $recipients),
			'emails' => elgg_extract('emails', $recipients),
			'subscribers' => elgg_extract('subscribers', $recipients),
			'members' => elgg_extract('members', $recipients),
		];
		
		// sticky form
		$sticky_values = elgg_get_sticky_values('newsletter/edit/recipients');
		if (!empty($sticky_values)) {
			foreach ($sticky_values as $name => $value) {
				$defaults[$name] = $value;
			}
			
			elgg_clear_sticky_form('newsletter/edit/recipients');
		}
		
		return $defaults;
	}
	
	/**
	 * Prepare schedule step
	 *
	 * @return array
	 */
	protected function prepareSchedule() {
		
		if ($this->entity->scheduled) {
			$date = Values::normalizeTime($this->entity->scheduled);
		} else {
			$date = Values::normalizeTime(time());
			$date->modify('+1 day');
		}
		
		$defaults = [
			'entity' => $this->entity,
			'scheduled' => $date->getTimestamp(),
			'show_in_archive' => isset($this->entity->show_in_archive) ? (int) $this->entity->show_in_archive : 1,
			'status_notification' => isset($this->entity->status_notification) ? $this->entity->status_notification : elgg_get_logged_in_user_entity()->email,
		];
		
		// sticky form
		$sticky_values = elgg_get_sticky_values('newsletter/edit/schedule');
		if (!empty($sticky_values)) {
			foreach ($sticky_values as $name => $value) {
				$defaults[$name] = $value;
			}
			
			elgg_clear_sticky_form('newsletter/edit/schedule');
		}
		
		return $defaults;
	}
}
