<?php

namespace ColdTrick\Newsletter;

/**
 * This image service is only used during rendering of newsletters to be able to prevent icons to be rendered as webp images.
 * Some email clients do not support webp images.
 */
class ImageService extends \Elgg\ImageService {

	/**
	 * {@inheritDoc}
	 */
	public function hasWebPSupport(): bool {
		return false;
	}
}
