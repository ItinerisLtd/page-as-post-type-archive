<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive\Types;

use WP_Post;

class ZeroFourZero extends AbstractType
{
    /**
     * @var string
     */
    public string $fieldName = 'zero-four-zero';

    public function __construct()
    {
        add_filter('body_class', [$this, 'bodyClass'], 10, 2);
        add_filter('display_post_states', [$this, 'addPageStates'], 10, 2);
        add_filter('facetwp_query_args', [$this, 'excludeSiteSearchPageFromResults'], 10, 2);
        add_action('admin_init', [$this, 'addSettingsField']);
        add_action('deleted_post', [$this, 'deletedPost']);
        add_action('transition_post_status', [$this, 'transitionPostStatus'], 10, 3);
        add_action('wp', [$this, 'disablePage']);
        add_action('template_redirect', [$this, 'customTemplate']);
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
            __('404 page', 'site'),
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

        $pageId = $this->getPageId();
        if ($pageId !== $post->ID) {
            return $post_states;
        }

        $post_states[] = '404 page';

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

        $pageId = $this->getPageId();
        if ($pageId !== $post->ID) {
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
        $pageId = $this->getPageId();
        if ($pageId !== $post_id) {
            return;
        }

        delete_option("page_for_{$this->fieldName}");
        flush_rewrite_rules();
    }

    public function bodyClass($classes): array {
        if (! is_search_page()) {
            return $classes;
        }

        $classes[] = 'zero-four-zero-page';

        return $classes;
    }

    /**
     * Exclude the site 404 page from the search results.
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

        $page_id = $this->getPageId();
        if (0 === $page_id) {
            return $query_args;
        }

        $post__not_in = (array) ($query_args['post__not_in'] ?? []);

        $query_args['post__not_in'] = array_merge($post__not_in, [$page_id]);

        return $query_args;
    }

    public function disablePage(): void {
        if (! is_page()) {
            return;
        }

        $pageId = $this->getPageId();
        if (0 === $pageId || ! is_page($pageId)) {
            return;
        }

        $wp_query = $GLOBALS['wp_query'];
        $wp_query->set_404();
        status_header(404);
    }

    /**
     * Use custom template.
     *
     * TODO: use template_include instead of this.
     */
    public function customTemplate(): void
    {
        if (is_admin() || ! is_404()) {
            return;
        }

        $pageId = $this->getPageId();
        if (0 === $pageId) {
            return;
        }

        $content = get_the_content(null, false, $pageId);

        echo $this->locateTemplate($this->fieldName, [
            'content' => $content,
        ]);
        exit;
    }
}
