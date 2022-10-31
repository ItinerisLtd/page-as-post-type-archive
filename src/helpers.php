<?php

declare(strict_types=1);

if (! function_exists('get_page_for_post_type')) {
    /**
     * Get the page ID for the given or current post type
     *
     * @param string|null $post_type
     * @return int|null
     */
    function get_page_for_post_type(?string $post_type = null): ?int
    {
        if (! $post_type && is_post_type_archive()) {
            $post_type = get_queried_object()->name;
        }

        if (! $post_type && is_singular()) {
            $post_type = get_queried_object()->post_type;
        }

        if (! $post_type && in_the_loop()) {
            $post_type = get_post_type();
        }

		if (empty($post_type) || ! in_array($post_type, get_post_types(), true)) {
			return null;
		}

	    return (int) get_option("page_for_{$post_type}", 0);
    }
}

if (! function_exists('get_page_for_post_type_object')) {
    /**
     * Get the page object for the given or current post type
     *
     * @param string|null $post_type
     * @return WP_Post|null
     */
    function get_page_for_post_type_object(?string $post_type = null): ?WP_Post
    {
        $page = get_page_for_post_type($post_type);

        if (null === $page) {
            return null;
        }

        return get_post($page);
    }
}

if (! function_exists('is_search_page')) {
    /**
     * @return bool
     */
    function is_search_page(): bool
    {
        if (! is_page()) {
            return false;
        }

        $custom_search_page_id = (int) get_option('page_for_search');
        if (0 === $custom_search_page_id) {
            return false;
        }

        return get_the_ID() === $custom_search_page_id;
    }
}

if (! function_exists('get_archive_page_id')) {
	/**
	 * @param string $postType
	 *
	 * @return int
	 */
    function get_archive_page_id(string $postType): int
    {
	    return (int) get_option("page_for_{$postType}", 0);
    }
}
