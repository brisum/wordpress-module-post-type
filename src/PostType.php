<?php

namespace Brisum\Wordpress\PostType\PostType;

use Brisum\Lib\ArrayStorage;
use WP_Post;

class PostType
{
	protected static $cachedPosts = [];

	/**
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * @var ArrayStorage
	 */
	protected $postMeta;

	/**
	 * PostType constructor.
	 * @param WP_Post $post
	 */
	protected function __construct($post)
	{
		$this->post = $post;
	}

	/**
	 * @param WP_Post $post
	 * @return mixed
	 */
	public static function create($post)
	{
		if (!isset(static::$cachedPosts[$post->ID])) {
			$product = new static($post);
			static::$cachedPosts[$post->ID] = $product;
		}

		return static::$cachedPosts[$post->ID];
	}

	/**
	 * @return ArrayStorage
	 */
	public function getPostMeta()
	{
		if (!$this->postMeta) {
			$this->postMeta = new ArrayStorage(get_post_meta($this->post->ID));
		}

		return $this->postMeta;
	}
}
