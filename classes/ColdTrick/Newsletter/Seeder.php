<?php

namespace ColdTrick\Newsletter;

use Elgg\Database\Seeds\Seed;
use Elgg\Exceptions\Seeding\MaxAttemptsException;
use Elgg\Values;

/**
 * Newsletter seeder
 */
class Seeder extends Seed {
	
	/**
	 * {@inheritdoc}
	 */
	public function seed() {
		$this->advance($this->getCount());
		
		$session_manager = elgg()->session_manager;
		$logged_in = $session_manager->getLoggedInUser();
		$admin = $this->createUser([
			'admin' => true,
			'validated' => true,
		]);
		$session_manager->setLoggedInUser($admin);
		
		$site = elgg_get_site_entity();
		$plugin = elgg_get_plugin_from_id('newsletter');
		$allow_site = $plugin->allow_site === 'yes';
		$allow_group = $plugin->allow_groups === 'yes';
		$custom_from = $plugin->custom_from === 'yes';
		
		while ($this->getCount() < $this->limit) {
			$container = null;
			if ($allow_site && $allow_group) {
				if ($this->faker()->boolean(70)) {
					$container = $this->getRandomGroup();
				} else {
					$container = $site;
				}
			} elseif ($allow_site) {
				$container = $site;
			} elseif ($allow_group) {
				$container = $this->getRandomGroup();
			} else {
				// unable to generate entities
				break;
			}
			
			if ($container instanceof \ElggGroup) {
				$container->enableTool('newsletter');
			}
			
			// custom from (optional)
			$from = null;
			if ($custom_from && $this->faker()->boolean(20)) {
				$from = $this->getRandomEmail();
			}
			
			// custom subject (optional)
			$subject = null;
			if ($this->faker()->boolean(20)) {
				$subject = $this->faker()->sentence();
			}
			
			try {
				/* @var $entity \Newsletter */
				$entity = $this->createObject([
					'subtype' => \Newsletter::SUBTYPE,
					'owner_guid' => $container->guid,
					'container_guid' => $container->guid,
					'subject' => $subject,
					'from' => $from,
				]);
			} catch (MaxAttemptsException $e) {
				// unable to generate entity with the given options
				continue;
			}
			
			$this->advance();
			
			// go through the different stages of a newsletter
			if (!$this->selectTemplate($entity)) {
				continue;
			}
			
			if (!$this->addContent($entity)) {
				continue;
			}
			
			if (!$this->addRecipients($entity)) {
				continue;
			}
			
			if (!$this->schedule($entity)) {
				continue;
			}
			
			if (!$this->send($entity)) {
				continue;
			}
			
			$this->createLikes($entity);
		}
		
		// cleanup
		$admin->delete();
		
		if ($logged_in) {
			$session_manager->setLoggedInUser($logged_in);
		} else {
			$session_manager->removeLoggedInUser();
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function unseed() {
		/* @var $entities \ElggBatch */
		$entities = elgg_get_entities([
			'type' => 'object',
			'subtype' => \Newsletter::SUBTYPE,
			'metadata_name' => '__faker',
			'limit' => false,
			'batch' => true,
			'batch_inc_offset' => false,
		]);
		
		/* @var $entity \Newsletter */
		foreach ($entities as $entity) {
			if ($entity->delete()) {
				$this->log("Deleted newsletter {$entity->guid}");
			} else {
				$this->log("Failed to delete newsletter {$entity->guid}");
				$entities->reportFailure();
				continue;
			}
			
			$this->advance();
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function getType(): string {
		return \Newsletter::SUBTYPE;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function getDefaultLimit(): int {
		return 5;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getCountOptions(): array {
		return [
			'type' => 'object',
			'subtype' => \Newsletter::SUBTYPE,
		];
	}
	
	/**
	 * Select the template the newsletter will be using
	 *
	 * @param \Newsletter $entity newsletter
	 *
	 * @return bool
	 */
	protected function selectTemplate(\Newsletter $entity): bool {
		if ($this->faker()->boolean(5)) {
			return false;
		}
		
		$templates = newsletter_get_available_templates($entity->container_guid, $entity);
		if (empty($templates)) {
			return false;
		}
		
		unset($templates['custom']); // not supported during seeding
		
		$template_names = array_keys($templates);
		$key = array_rand($template_names);
		
		$entity->template = $template_names[$key];
		
		return true;
	}
	
	/**
	 * Add content to the newsletter
	 *
	 * @param \Newsletter $entity newsletter
	 *
	 * @return bool
	 */
	protected function addContent(\Newsletter $entity): bool {
		if ($this->faker()->boolean(5)) {
			return false;
		}
		
		$content = '';
		for ($i = 0; $i < $this->faker()->numberBetween(2, 5); $i++) {
			$content .= elgg_format_element('p', [], $this->faker()->sentences($this->faker()->numberBetween(5, 10), true));
		}
		
		$entity->content = $content;
		
		return true;
	}
	
	/**
	 * Add recipients to the newsletter
	 *
	 * @param \Newsletter $entity newsletter
	 *
	 * @return bool
	 */
	protected function addRecipients(\Newsletter $entity): bool {
		if ($this->faker()->boolean(5)) {
			return false;
		}
		
		$recipients = [
			'user_guids' => [],
			'group_guids' => [],
			'emails' => [],
			'subscribers' => (int) $this->faker()->boolean(70),
			'members' => (int) $this->faker()->boolean(70),
		];
		
		if ($this->faker()->boolean(15)) {
			for ($i = 0; $i < $this->faker()->numberBetween(0, 20); $i++) {
				$recipients['user_guids'][] = $this->getRandomUser($recipients['user_guids'])->guid;
			}
		}
		
		if ($this->faker()->boolean(15)) {
			for ($i = 0; $i < $this->faker()->numberBetween(0, 5); $i++) {
				$recipients['group_guids'][] = $this->getRandomGroup($recipients['group_guids'])->guid;
			}
		}
		
		if ($this->faker()->boolean(15)) {
			for ($i = 0; $i < $this->faker()->numberBetween(0, 20); $i++) {
				$recipients['emails'][] = $this->getRandomEmail();
			}
		}
		
		$entity->setRecipients($recipients);
		
		return true;
	}
	
	/**
	 * Schedule a newsletter for delivery
	 *
	 * @param \Newsletter $entity newletter
	 *
	 * @return bool
	 */
	protected function schedule(\Newsletter $entity): bool {
		if ($this->faker()->boolean(5)) {
			return false;
		}
		
		$since = $this->create_since;
		$this->setCreateSince($entity->time_created);
		
		$date = Values::normalizeTime($this->getRandomCreationTimestamp());
		$date->modify('midnight');
		$hours = $this->faker()->numberBetween(0, 23);
		$date->modify("+{$hours} hours");
		
		$this->setCreateSince($since);
		
		$entity->scheduled = $date->getTimestamp();
		$entity->status = 'scheduled';
		$entity->show_in_archive = (int) $this->faker()->boolean(70);
		$entity->status_notification = $this->getRandomEmail();
		
		return true;
	}
	
	/**
	 * Send the newsletter
	 *
	 * @param \Newsletter $entity newsletter
	 *
	 * @return bool
	 */
	protected function send(\Newsletter $entity): bool {
		if ($this->faker()->boolean(5)) {
			return false;
		}
		
		$since = $this->create_since;
		$this->setCreateSince($entity->time_created);
		
		$start_time = $this->getRandomCreationTimestamp();
		$logging = [
			'start_time' => $start_time,
		];
		
		$this->setCreateSince($since);
		
		$entity->status = 'sending';
		$entity->start_time = $logging['start_time'];
		
		$filtered_recipients = newsletter_get_filtered_recipients($entity);
		if (empty($filtered_recipients)) {
			$entity->status = 'sent';
			
			return true;
		}
		
		foreach ($filtered_recipients as $type => $recipients) {
			if (empty($recipients)) {
				continue;
			}
			
			foreach ($recipients as $id => $recipient) {
				$start_time++;
				
				$recipient_log = [
					'type' => $type,
					'email' => $recipient,
					'time' => date(DATE_RFC1123, $start_time),
					'timestamp' => $start_time,
					'status' => $this->faker()->boolean(95),
				];
				
				if ($type === 'users') {
					$recipient_log['guid'] = $id;
					$recipient_log['status'] = true;
				}
				
				if ($recipient_log['status'] && !empty($recipient_log['guid'])) {
					$entity->addRelationship($recipient_log['guid'], \Newsletter::SEND_TO);
				}
				
				// add to logging
				$logging['recipients'][] = $recipient_log;
				$entity->saveLogging($logging);
			}
		}
		
		$start_time++;
		
		// log the end
		$logging['end_time'] = $start_time;
		
		$entity->saveLogging($logging);
		
		// set newsletter status to done
		$entity->status = 'sent';
		
		return true;
	}
}
