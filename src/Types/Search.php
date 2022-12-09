<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive\Types;

use WP_Post;

class Search extends AbstractType
{
    /**
     * @var string
     */
    protected string $fieldName = 'search';

    /**
     * @var int|null
     */
    protected ?int $customPageId = null;

    public function __construct()
    {
        add_filter('body_class', [$this, 'bodyClass'], 10, 2);
        add_filter('display_post_states', [$this, 'addPageStates'], 10, 2);
        add_filter('get_search_form', [$this, 'getSearchForm']);
        add_filter('facetwp_query_args', [$this, 'excludeSiteSearchPageFromResults'], 10, 2);
        add_action('admin_init', [$this, 'addSettingsField']);
        add_action('deleted_post', [$this, 'deletedPost']);
        add_action('transition_post_status', [$this, 'transitionPostStatus'], 10, 3);
        add_action('wp', [$this, 'redirectUser']);
    }

    public function addSettingsField(): void
    {
        $setting_id = "page_for_{$this->fieldName}";

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
            __('Search custom page', 'site'),
            [__CLASS__, 'pageSelector'],
            'reading',
            'default',
            [
                'setting_id' => $setting_id,
                'post_type' => $this->fieldName,
            ]
        );
    }

    public function addPageStates(array $post_states, WP_Post $post): array
    {
        if ('page' !== $post->post_type) {
            return $post_states;
        }

        $searchPageId = $this->getPageId();
        if ($searchPageId !== $post->ID) {
            return $post_states;
        }

        $post_states[] = 'Search custom page';

        return $post_states;
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

        $searchPageId = $this->getPageId();
        if ($searchPageId !== $post->ID) {
            return;
        }

        delete_option("page_for_{$this->fieldName}");
        flush_rewrite_rules();
    }

    /**
     * Delete relevant option if a page for the archive is deleted
     *
     * @param int $post_id
     */
    public function deletedPost(int $post_id): void
    {
        $searchPageId = $this->getPageId();
        if ($searchPageId !== $post_id) {
            return;
        }

        delete_option("page_for_{$this->fieldName}");
        flush_rewrite_rules();
    }

    public function bodyClass($classes): array {
        if (! is_search_page()) {
            return $classes;
        }

        $classes[] = 'search-page';

        return $classes;
    }

    public function getSearchForm (string $form): string {
        $custom_search_page_id = $this->getPageId();
        if (0 === $custom_search_page_id) {
            return $form;
        }

        $searchFieldName = apply_filters('itineris/page-as-post-type-archive/search_field_name', '_search');

        $form = str_replace('name="s"', 'name="' . $searchFieldName . '"', $form);
        return preg_replace(
            '/action=".*"/',
            'action="' . get_permalink($custom_search_page_id) . '"',
            $form
        );
    }

    /**
     * Redirect user to a custom search page
     *
     * @return void
     */
    public function redirectUser (): void {
        if (! is_search() || is_admin()) {
            return;
        }

        $custom_search_page_id = $this->getPageId();
        if (0 === $custom_search_page_id) {
            return;
        }

        $custom_search_page = get_permalink($custom_search_page_id);

        $keyword = get_search_query();
        if (empty($keyword)) {
            wp_safe_redirect($custom_search_page, 301);
        }

        $searchFieldName = apply_filters('itineris/page-as-post-type-archive/search_field_name', '_search');

        $url = add_query_arg(
            [
                $searchFieldName => get_search_query(),
            ],
            $custom_search_page
        );

        wp_safe_redirect($url, 301);
    }

    /**
     * Exclude the site search page from the search results.
     *
     * @param array            $query_args
     * @param \FacetWP_Renderer $class
     * @return array
     */
    public function excludeSiteSearchPageFromResults(array $query_args, \FacetWP_Renderer $class): array
    {
        if ('search' !== $class->ajax_params['template']) {
            return $query_args;
        }

        $custom_search_page_id = $this->getPageId();
        if (0 === $custom_search_page_id) {
            return $query_args;
        }

        $post__not_in = (array) ($query_args['post__not_in'] ?? []);

        $query_args['post__not_in'] = array_merge($post__not_in, [$custom_search_page_id]);

        return $query_args;
    }
}
