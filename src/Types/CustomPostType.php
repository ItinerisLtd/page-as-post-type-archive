<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive\Types;

use WP_Post;
use WP_Post_Type;

class CustomPostType extends AbstractType {
	/**
	 * @var array|null
	 */
	public ?array $postTypes = null;

	/**
	 * @var array|null
	 */
	public ?array $archivePages = null;

	public function __construct()
	{
		add_filter('display_post_states', [$this, 'addPageStates'], 10, 2);
		add_filter('register_post_type_args', [$this, 'registerPostTypeArgs'], 10, 2);
		add_filter('wpseo_breadcrumb_links', [$this, 'breadcrumbLinks']);
		add_action('template_redirect', [$this, 'customTemplate']);
		add_action('admin_init', [$this, 'addCustomPostTypePageSelectorOptions']);
		add_action('deleted_post', [$this, 'deletedPost']);
		add_action('transition_post_status', [$this, 'transitionPostStatus'], 10, 3);
	}

	/**
	 * Get all available post types.
	 *
	 * @return array
	 */
	public function getPostTypes(): array {
		if (null === $this->postTypes) {
			$post_type_to_ignore = apply_filters('itineris/page-as-post-type-archive/post_type_to_ignore', []);

			$this->postTypes = array_filter(
				get_post_types([], 'objects'),
				fn (WP_Post_Type $postType): bool =>
					($postType->has_archive && ! $postType->_builtin
					 && ! in_array($postType->name, $post_type_to_ignore, true))
					|| 'post' === $postType->name
			);
		}

		return $this->postTypes ?? [];
	}

	public function addCustomPostTypePageSelectorOptions(): void
	{
		$post_types = $this->getPostTypes();
		if (empty($post_types)) {
			return;
		}

		foreach ($post_types as $post_type) {
			if ('post' === $post_type) {
				continue;
			}

			$this->addSettingsField($post_type);
		}
	}

	public function addSettingsField(WP_Post_Type $post_type): void
	{
		$setting_id = "page_for_{$post_type->name}";

		register_setting(
			'reading',
			$setting_id,
			[
				'type' => 'string',
				'sanitize_callback' => [$this, 'saveSettingsCallback'],
				'default' => NULL,
			]
		);

		add_settings_field(
			$setting_id, // ID
			"{$post_type->label} Archive Page",
			[__CLASS__, 'pageSelector'],
			'reading',
			'default',
			[
				'setting_id' => $setting_id,
				'post_type' => $post_type->name,
			]
		);
	}

	public function addPageStates(array $post_states, ?WP_Post $post): array
	{
		if (! $post instanceof WP_Post || 'page' !== $post->post_type) {
			return $post_states;
		}

		$archive_page_data = $this->getArchivePageData($post->ID);
		if (null === $archive_page_data) {
			return $post_states;
		}

		$post_states[] = "{$archive_page_data->label} Archive Page";

		return $post_states;
	}

	protected function getArchivePageData(int $postId): ?object
	{
		if (null === $this->archivePages) {
			$saved_settings = array_map(fn (WP_Post_Type $postType): object => (object) [
				'name' => $postType->name,
				'label' => $postType->label,
				'option_name' => "page_for_{$postType->name}",
				'value' => (int) get_option("page_for_{$postType->name}", 0),
			], $this->getPostTypes());

			$this->archivePages = $saved_settings;
		}

		if (empty($this->archivePages)) {
			return null;
		}

		foreach ($this->archivePages as $setting) {
			if ($postId === $setting->value) {
				return $setting;
			}
		}

		return null;
	}

	/**
	 * Delete the setting for the corresponding post type if the page status
	 * is transitioned to anything other than published
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param WP_Post $post
	 */
	public function transitionPostStatus(string $new_status, string $old_status, WP_Post $post): void
	{
		if ('publish' === $new_status) {
			return;
		}

		$archive_page_data = $this->getArchivePageData($post->ID);
		if (null === $archive_page_data) {
			return;
		}

		delete_option($archive_page_data->option_name);
		flush_rewrite_rules();
	}

	/**
	 * Delete relevant option if a page for the archive is deleted
	 *
	 * @param int $post_id
	 */
	public function deletedPost(int $post_id): void
	{
		$archive_page_data = $this->getArchivePageData($post_id);
		if (null === $archive_page_data) {
			return;
		}

		delete_option($archive_page_data->option_name);
		flush_rewrite_rules();
	}

	public function registerPostTypeArgs(array $args, string $post_type): array
	{
		$post_type_page = (int) get_option("page_for_{$post_type}", 0);
		if (0 === $post_type_page) {
			return $args;
		}

		// make sure we don't create rules for an unpublished page preview URL.
		if ('publish' !== get_post_status($post_type_page)) {
			return $args;
		}

		// get the custom archive page slug.
		$slug = get_permalink($post_type_page);
		$slug = str_replace(home_url(), '', $slug);
		$slug = trim($slug, '/');

		if (isset($args['rewrite']['slug'])) {
			$args['rewrite']['slug'] = $slug;
		}

		$args['has_archive'] = $slug;

		return $args;
	}

	/**
	 * Use custom template.
	 *
	 * TODO: use template_include instead of this.
	 */
	public function customTemplate(): void
	{
		$postTypes = array_keys($this->getPostTypes());
		if (empty($postTypes)) {
			return;
		}

		if (! is_post_type_archive($postTypes) && ! is_home()) {
			return;
		}

		if (is_home()) {
			$postType = 'post';
		} else {
			$postType = get_queried_object()->name;
		}

		$postTypeOptionName = $postType;
		if ('post' === $postType) {
			$postTypeOptionName = 'posts';
		}

		$post_type_page = (int) get_option("page_for_{$postTypeOptionName}", 0);
		if (0 === $post_type_page) {
			return;
		}

		$content = get_the_content(null, false, $post_type_page);
		echo $this->locateTemplate($postType, [
			'content' => $content,
		]);
		exit;
	}

    public function breadcrumbLinks(array $links): array
    {
        $postTypes = array_keys($this->getPostTypes());
        if (empty($postTypes)) {
            return $links;
        }

        if (! is_singular($postTypes) && ! is_post_type_archive($postTypes)) {
            return $links;
        }

        $post_type = get_post_type();
        if (false === $post_type) {
            return $links;
        }

        $archive_page_id = $this->getPageIdByPostType($post_type);
        if (0 === $archive_page_id) {
            return $links;
        }

        $pages_id = [$archive_page_id];
        $ancestors = get_post_ancestors($archive_page_id);
        $pages_id = array_reverse(array_merge($pages_id, $ancestors));

        $breadcrumbs = [];

        foreach ($pages_id as $crumb) {
            $breadcrumbs[] = [
                'url' => get_permalink($crumb),
                'text' => get_the_title($crumb),
            ];
        }

        if (is_single()) {
            array_splice($links, 1, -1, $breadcrumbs);
        } else {
            array_splice($links, 1, 1, $breadcrumbs);
        }

        return $links;
    }
}
