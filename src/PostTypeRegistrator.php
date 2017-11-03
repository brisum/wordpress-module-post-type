<?php

namespace Brisum\Wordpress\PostType;

use Brisum\Wordpress\CustomField\MetaBox;
use Exception;
use WP_Error;
use WP_Rewrite;

class PostTypeRegistrator
{
	/**
	 * @var string
	 */
	protected $postType;
	
	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var array
	 */
	protected $rewriteRules;

	/**
	 * @var array
	 */
	protected $metaBoxes = [];

	public function __construct()
	{
		add_action('init', [$this, 'registerPostType']);
		add_action('init', [$this, 'addMetaBoxes']);

		add_filter("{$this->postType}_rewrite_rules", [$this, 'filterPostTypeRewriteRules']);
		add_action("generate_rewrite_rules", [$this, 'actionGenerateRewriteRules']);
	}

	/**
	 * @throws Exception
	 */
	public function registerPostType()
	{
		$postType = $this->config ? register_post_type($this->postType, $this->config) : null;

		if (is_wp_error($postType)) {
			/** @var WP_Error $postType */
			throw new Exception($postType->get_error_message());
		}
	}

	/**
	 * @param array $rewriteRules
	 * @return array
	 */
	public function filterPostTypeRewriteRules($rewriteRules)
	{
		return null === $this->rewriteRules ? $rewriteRules : $this->rewriteRules;
	}

	/**
	 * @param WP_Rewrite $wp_rewrite
	 * @return WP_Rewrite
	 */
	public function actionGenerateRewriteRules(WP_Rewrite $wp_rewrite)
	{
		if (null !== $this->rewriteRules) {
			foreach ($wp_rewrite->rules as $regexp => $url) {
				if (0 === strpos($regexp, $this->postType)) {
					unset($wp_rewrite->rules[$regexp]);
				}
			}
			foreach ($wp_rewrite->extra_rules_top as $regexp => $url) {
				if (0 === strpos($regexp, $this->postType)) {
					unset($wp_rewrite->rules[$regexp]);
				}
			}
		}

		return $wp_rewrite;
	}

	/**
	 * @return void
	 */
	public function addMetaBoxes()
	{
		foreach ($this->metaBoxes as $name => $metaBoxConfig) {
			$metaBoxConfig['settings']['screen'] = $this->postType;
			new MetaBox($metaBoxConfig);
		}
	}
}
