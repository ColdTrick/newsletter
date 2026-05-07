<?php

namespace ColdTrick\Newsletter\Controllers;

use Elgg\Controllers\GenericContentListing;
use Elgg\Exceptions\Http\BadRequestException;

/**
 * List Newsletters
 */
class ListNewsletters extends GenericContentListing {
	
	/**
	 * {@inheritdoc}
	 */
	protected function getListingOptions(string $page, array $options): array {
		$filter = false;
		$options['metadata_name_value_pairs'] = [];
		
		switch ($page) {
			case 'all':
			case 'site':
				$site = elgg_get_site_entity();
				$filter = 'sent';
				if ($site->canEdit()) {
					$filter = $this->request->getParam('filter') ?: $filter;
				}
				
				$options['container_guid'] = $site->guid;
				
				break;
			case 'group':
				$filter = 'sent';
				if ($this->page_owner->canEdit()) {
					$filter = $this->request->getParam('filter') ?: $filter;
				}
				break;
			case 'received':
				$options['metadata_name_value_pairs'][] = [
					'name' => 'status',
					'value' => 'sent',
				];
				$options['relationship'] = \Newsletter::SEND_TO;
				$options['relationship_guid'] = $this->page_owner->guid;
				$options['inverse_relationship'] = true;
				$options['sort_by'] = [
					'property' => 'start_time',
					'direction' => 'DESC',
					'signed' => true,
				];
				break;
		}
		
		switch ($filter) {
			case 'concept':
				$options['metadata_name_value_pairs'][] = [
					'name' => 'status',
					'value' => 'concept',
				];
				break;
			case 'scheduled':
				$options['metadata_name_value_pairs'][] = [
					'name' => 'status',
					'value' => 'scheduled',
				];
				$options['sort_by'] = [
					'property' => 'scheduled',
					'direction' => 'ASC',
					'signed' => true,
				];
				break;
			case 'sending':
				$options['metadata_name_value_pairs'][] = [
					'name' => 'status',
					'value' => 'sending',
				];
				break;
			case 'sent':
				$options['metadata_name_value_pairs'][] = [
					'name' => 'status',
					'value' => 'sent',
				];
				if (!elgg_is_admin_logged_in()) {
					$options['metadata_name_value_pairs'][] = [
						'name' => 'show_in_archive',
						'value' => 1,
					];
				}
				
				$options['sort_by'] = [
					'property' => 'start_time',
					'direction' => 'DESC',
					'signed' => true,
				];
				break;
		}
		
		return $options;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getPageOptions(string $page, array $options): array {
		$options = parent::getPageOptions($page, $options);
		
		if ($this->page_owner instanceof \ElggGroup) {
			$options['filter_id'] = 'newsletter/group';
		} else {
			$options['filter_id'] = 'newsletter';
		}
		
		switch ($page) {
			case 'all':
			case 'group':
			case 'site':
				$options['filter_value'] = $this->request->getParam('filter') ?: 'sent';
				
				break;
		}
		
		return $options;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function listAll(array $options): string {
		$site = elgg_get_site_entity();
		elgg_set_page_owner_guid($site->guid);
		$this->page_owner = $site;
		
		newsletter_register_title_menu_items($site);
		
		elgg_register_title_button('add', 'object', \Newsletter::SUBTYPE);
		
		return parent::listAll($options);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function listGroup(array $options): string {
		newsletter_register_title_menu_items($this->page_owner);
		
		return parent::listGroup($options);
	}
	
	/**
	 * List received Newsletters for a user
	 *
	 * @param array $options listing options
	 *
	 * @return string
	 */
	protected function listReceived(array $options): string {
		if (!$this->page_owner instanceof \ElggUser) {
			throw new BadRequestException();
		}
		
		elgg_push_collection_breadcrumbs($options['type'], $options['subtype']);
		
		return elgg_view_page('', $this->getPageOptions('received', [
			'title' => elgg_echo("collection:{$options['type']}:{$options['subtype']}:received", [$this->page_owner->getDisplayName()]),
			'content' => elgg_view('page/list/all', [
				'entity' => $this->page_owner,
				'options' => $options,
				'page' => 'received',
			]),
			'filter' => false,
		]));
	}
	
	/**
	 * List site Newsletters
	 *
	 * @param array $options listing options
	 *
	 * @return string
	 */
	protected function listSite(array $options): string {
		return $this->listAll($options);
	}
}
