<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive\Integrations;

/**
 * Inpsyde MultilingualPress integration.
 */

final class MultilingualPress {
    public function __construct()
    {
        if (
            ! is_plugin_active('multilingualpress/multilingualpress.php')
            || ! class_exists('\Inpsyde\MultilingualPress\Framework\Api\Translation')
        )
        {
            return;
        }

        add_filter(
            \Inpsyde\MultilingualPress\Framework\Api\Translation::FILTER_URL,
            [$this, 'translateMultilingualPressArchivePageUrl'],
            10,
            4,
        );
    }

    public static function init(): self
    {
        return new self();
    }

    /**
     * MultilingualPress Archive Page URL translation.
     *
     * @param string $remoteUrl
     * @param int $remoteSiteId
     * @param int $remoteContentId
     * @param \Inpsyde\MultilingualPress\Framework\Api\Translation $translation
     * @return string
     */
    public static function translateMultilingualPressArchivePageUrl(
        string $remoteUrl,
        int $remoteSiteId,
        int $remoteContentId,
        \Inpsyde\MultilingualPress\Framework\Api\Translation $translation
    ): string {
        if (
            ! class_exists('\Inpsyde\MultilingualPress\Framework\WordpressContext')
            || ! is_archive()
            || \Inpsyde\MultilingualPress\Framework\WordpressContext::TYPE_POST_TYPE_ARCHIVE !== $translation->type()
        ) {
            return $remoteUrl;
        }

        $postType = get_queried_post_type();
        switch_to_blog($remoteSiteId);
        $remoteArchivePageId = get_archive_page_id($postType);
        if (0 !== $remoteArchivePageId) {
            $remoteUrl = get_permalink($remoteArchivePageId);
        }
        restore_current_blog();

        return $remoteUrl;
    }
}