<?php

namespace Brisum\Wordpress\PostType\PostType;

use Exception;
use WP_Error;

class Taxonomies
{
	/**
	 * @var string
	 */
	protected $postType;

	/**
	 * @var array
	 */
	protected $taxonomies = [];

	/**
	 * Taxonomies constructor.
	 */
	public function __construct()
	{
		add_action('init', [$this, 'actionInit']);
	}

	public function actionInit()
	{
		foreach ($this->taxonomies as $name => $args) {
			$taxonomy = register_taxonomy($name, $this->postType, $args);

			if (is_wp_error($taxonomy)) {
				/** @var WP_Error $taxonomy */
				throw new Exception($taxonomy->get_error_message());
			}
		}
	}
}
