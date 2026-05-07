<?php

namespace ColdTrick\Newsletter\Controllers;

use Elgg\Controllers\EntityEditAction;
use Elgg\Exceptions\Http\BadRequestException;
use Elgg\Http\OkResponse;

/**
 * create/edit a Newsletter
 */
class EditAction extends EntityEditAction {
	
	/**
	 * {@inheritdoc}
	 */
	protected function validate(): void {
		parent::validate();
		
		if (elgg_get_plugin_setting('custom_from', 'newsletter') === 'yes') {
			$from = $this->request->getParam('from');
			
			if (!newsletter_validate_custom_from($from)) {
				throw new BadRequestException(elgg_echo('newsletter:action:edit:error:from'));
			}
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function success(?string $forward_url = null): OkResponse {
		if ($this->isNewEntity()) {
			$forward_url = elgg_generate_entity_url($this->entity, 'edit', 'template');
		} else {
			$forward_url = REFERRER;
		}
		
		return parent::success($forward_url);
	}
}
