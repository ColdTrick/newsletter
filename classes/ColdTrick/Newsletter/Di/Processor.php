<?php

namespace ColdTrick\Newsletter\Di;

use Elgg\Email;
use Elgg\Traits\Di\ServiceFacade;
use Symfony\Component\Mime\Address;

/**
 * Process a newsletter for sending
 */
class Processor {
	
	use ServiceFacade;
	
	protected ?\Newsletter $entity = null;
	
	protected ?\ElggEntity $container = null;
	
	protected array $banned_users_cache = [];
	
	/**
	 * Create a service
	 *
	 * @param Recipients $recipients recipients service
	 */
	public function __construct(protected Recipients $recipients) {
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function name(): string {
		return 'newsletter.processor';
	}
	
	/**
	 * Process the newsletter
	 *
	 * @param int $guid newsletter guid
	 *
	 * @return void
	 */
	public function process(int $guid): void {
		$this->reset();
		
		if (!$this->loadNewsletter($guid)) {
			return;
		}
		
		// sending could take a while
		set_time_limit(0);
		
		try {
			elgg_call(ELGG_IGNORE_ACCESS, function () {
				$logging = ['start_time' => time()];
				
				// set newsletter status to sending
				$this->entity->status = 'sending';
				$this->entity->start_time = $logging['start_time'];
				
				// get the recipients
				$filtered_recipients = $this->recipients->getFilteredRecipients($this->entity);
				if (empty($filtered_recipients)) {
					// no recipients so report error
					$this->entity->status = 'sent';
					
					return;
				}
				
				$this->preloadRecipientBannedCache(array_keys($filtered_recipients['users']));
				
				// get newsletter content
				$message_html_content = $this->getHtmlBody();
				
				// set default send options
				$send_options = [
					'from' => $this->getFrom(),
					'subject' => $this->getSubject(),
					'body' => $this->getPlainBody(),
				];
				
				$save_recipient_logging = function ($recipient_log) use (&$logging) {
					$logging['recipients'][] = $recipient_log;
					
					$this->entity->saveLogging($logging);
				};
				
				foreach ($filtered_recipients as $type => $recipients) {
					if (empty($recipients)) {
						continue;
					}
					
					foreach ($recipients as $id => $recipient) {
						$recipient_log = [
							'type' => $type,
							'email' => $recipient,
							'time' => date(DATE_RFC1123),
							'timestamp' => time(),
							'status' => false,
						];
						
						// create individual footer for unsubscribe link
						if ($type === 'users') {
							$recipient_log['guid'] = $id;
							
							if ($this->isRecipientBanned($id)) {
								$save_recipient_logging($recipient_log);
								continue;
							}
							
							$unsubscribe_link = newsletter_generate_unsubscribe_link($this->container, $id);
						} else {
							$unsubscribe_link = newsletter_generate_unsubscribe_link($this->container, $recipient);
						}
						
						// place the unsubscribe link in the message
						$unsubscribe_link = elgg_normalize_url($unsubscribe_link);
						$message_html_content_user = str_ireplace(urlencode('{unsublink}'), $unsubscribe_link, $message_html_content);
						
						// replace the online link for logged-out users to add an email-address
						if ($type !== 'users') {
							$online_link = $this->entity->getURL();
							$new_online_link = elgg_http_add_url_query_elements($online_link, [
								'e' => $recipient,
							]);
							
							$message_html_content_user = str_ireplace($online_link, $new_online_link, $message_html_content_user);
						}
						
						// add URL postfix to all internal links
						$message_html_content_user = newsletter_apply_url_postfix($message_html_content_user, $this->entity);
						
						// send mail
						$send_options['to'] = $recipient;
						$send_options['params']['html_message'] = $message_html_content_user;
						
						$email = Email::factory($send_options);
						
						try {
							$recipient_log['status'] = elgg_send_email($email);
						} catch (\Throwable $t) {
							// some error during sending
						}
						
						if ($recipient_log['status'] && !empty($recipient_log['guid'])) {
							$this->entity->addRelationship($recipient_log['guid'], \Newsletter::SEND_TO);
						}
						
						// add to logging
						$save_recipient_logging($recipient_log);
					}
				}
				
				$logging['end_time'] = time();
				
				$this->entity->saveLogging($logging);
				
				// set newsletter status to done
				$this->entity->status = 'sent';
				
				// needed to trigger the update ts so we now something changed
				$this->entity->save();
				
				// send status notification
				$this->sendStatusNotification();
			});
		} catch (\Throwable $t) {
			// something happened
		}
		
		$this->reset();
	}
	
	/**
	 * Reset internal caches
	 *
	 * @return void
	 */
	protected function reset(): void {
		$this->entity = null;
		$this->container = null;
		$this->banned_users_cache = [];
	}
	
	/**
	 * Load the newsletter for processing
	 *
	 * @param int $guid entity GUID
	 *
	 * @return bool
	 */
	protected function loadNewsletter(int $guid): bool {
		if ($guid < 1) {
			return false;
		}
		
		$entity = elgg_call(ELGG_IGNORE_ACCESS, function() use ($guid) {
			return get_entity($guid);
		});
		
		if (!$entity instanceof \Newsletter) {
			return false;
		}
		
		$this->entity = $entity;
		$this->container = $entity->getContainerEntity();
		
		return true;
	}
	
	/**
	 * Get the newsletter subject
	 *
	 * @return string
	 */
	protected function getSubject(): string {
		if ($this->entity?->subject) {
			return $this->entity->subject;
		}
		
		return elgg_echo('newsletter:subject', [
			$this->container?->getDisplayName(),
			$this->entity?->getDisplayName(),
		]);
	}
	
	/**
	 * Get the plaintext body placeholder
	 *
	 * @return string
	 */
	protected function getPlainBody(): string {
		return elgg_echo('newsletter:plain_message', [$this->entity?->getURL()]);
	}
	
	/**
	 * Get the HTML body of the newsletter
	 *
	 * @return string
	 */
	protected function getHtmlBody(): string {
		return elgg_view_layout('newsletter', ['entity' => $this?->entity]);
	}
	
	/**
	 * Get the From (sending) address of the newsletter
	 *
	 * @return Address
	 */
	protected function getFrom(): Address {
		if ((elgg_get_plugin_setting('custom_from', 'newsletter') === 'yes') && !empty($this->entity->from)) {
			// from is validated to a valid email address in the newsletter save action
			return new Address($this->entity?->from, $this->container?->getDisplayName());
		}
		
		// default to the site email address
		$site = elgg_get_site_entity();
		
		return new Address($site->getEmailAddress(), $this->container?->getDisplayName());
	}
	
	/**
	 * Check if a recipient user is banned
	 *
	 * @param int $guid user guid
	 *
	 * @return bool
	 */
	protected function isRecipientBanned(int $guid): bool {
		if (elgg_get_plugin_setting('include_banned_users', 'newsletter')) {
			// banned users are allowed
			return false;
		}
		
		return in_array($guid, $this->banned_users_cache);
	}
	
	/**
	 * Load the banned user cache for processing a newsletter
	 *
	 * @param array $user_guids potential user recipients
	 *
	 * @return void
	 */
	protected function preloadRecipientBannedCache(array $user_guids): void {
		$this->banned_users_cache = elgg_get_metadata([
			'type' => 'user',
			'guids' => $user_guids,
			'limit' => false,
			'metadata_name_value_pairs' => [
				[
					'name' => 'banned',
					'value' => 'yes',
				],
			],
			'callback' => function ($row) {
				return (int) $row->entity_guid;
			}
		]);
	}
	
	/**
	 * Send a notification that processing is complete
	 *
	 * @return void
	 */
	protected function sendStatusNotification(): void {
		if (!newsletter_is_email_address($this->entity->status_notification)) {
			return;
		}
		
		elgg_send_email(Email::factory([
			'to' => $this->entity->status_notification,
			'subject' => elgg_echo('newsletter:status_notification:subject'),
			'body' => elgg_echo('newsletter:status_notification:message', [
				$this->entity->getDisplayName(),
				$this->entity->getURL(),
			]),
		]));
	}
}
