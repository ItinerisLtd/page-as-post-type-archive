<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive\Types;

abstract class AbstractType {
    /**
     * @var int|null
     */
    protected ?int $customPageId = null;

    /**
     * Locate template.
     *
     * Locate the called template.
     *
     * Search Order:
     * 1. /themes/theme/your-theme-name/$template_name
     * 2. /themes/theme/$template_name
     * 3. /plugins/your-plugin-name/views/$template_name.
     *
     * @param string $template Template to load.
     * @return string Path to the template file.
     */
    protected function locateTemplate(string $template, array $args = []): string {
        $template_path = ITINERIS_PAPTA_SLUG;

        if (view()->exists("{$template_path}.{$template}")) {
            return view("{$template_path}.{$template}", $args)->toHtml();
        }

        if (view()->exists("ItinerisPageAsPostTypeArchive::{$template}")) {
            return view("ItinerisPageAsPostTypeArchive::{$template}", $args)->toHtml();
        }

        if (view()->exists("{$template_path}.default")) {
            return view("{$template_path}.default", $args)->toHtml();
        }

        return view("ItinerisPageAsPostTypeArchive::default", $args)->toHtml();
    }

    public static function pageSelector(array $args)
    {
        extract($args);

        wp_dropdown_pages(
            [
                'name' => $setting_id,
                'echo' => true,
                'show_option_none' => __('&mdash; Select &mdash;'),
                'option_none_value' => '0',
                'selected' => (int) get_option($setting_id, 0),
            ]
        );
    }

    /**
     * Get page ID for current model.
     *
     * @return int
     */
    public function getPageId(): int
    {
        if (null === $this->customPageId) {
            $this->customPageId = $this->getPageIdByPostType($this->fieldName);
        }

        return $this->customPageId ?? 0;
    }

    /**
     * Get page ID by post type.
     *
     * @param string $postType
     * @return int
     */
    public function getPageIdByPostType(string $postType): int
    {
        $option = "page_for_{$postType}";
        if ('post' === $postType) {
            $option = 'page_for_posts';
        }

        $pageId = (int) get_option($option, 0);

        return apply_filters(
            'itineris/page-as-post-type-archive/get_page_id',
            $pageId,
            $postType
        );
    }

	/**
	 * @param mixed $value
	 * @return int
	 */
	public function saveSettingsCallback($value): int
	{
		flush_rewrite_rules();
		return intval($value);
	}
}
