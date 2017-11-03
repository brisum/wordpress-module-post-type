<?php

namespace Brisum\Wordpress\PostType;

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

			$filterMethod = $this->generateFilterMethod($name);
			if (method_exists($this, $filterMethod)) {
                add_filter("{$name}_rewrite_rules", [$this, $filterMethod]);
            }
		}
	}

    /**
     * @param string $permastruct
     * @return string
     */
    protected function generateFilterMethod($permastruct)
    {
        return 'filter'
                . ucfirst(preg_replace_callback('/[_-](.)/', [$this, 'replaceFilterMethod'], $permastruct))
                . 'RewriteRules';
    }

    /**
     * @param array $matches
     * @return string
     */
    public function replaceFilterMethod($matches)
    {
        return strtoupper($matches[1]);
    }
}
