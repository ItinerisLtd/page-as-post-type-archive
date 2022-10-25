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

    public function getPageId(): int
    {
        if (null === $this->customPageId) {
            $this->customPageId = (int) get_option("page_for_{$this->fieldName}", 0);
        }

        return $this->customPageId ?? 0;
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
