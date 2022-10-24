<?php

declare(strict_types=1);

/**
 * Plugin Name:     Page as post type archive
 * Plugin URI:      https://github.com/ItinerisLtd/page-as-post-type-archive/
 * Description:     Allows you to set a page as the archive of a post type.
 * Version:         0.1.0
 * Author:          Itineris Limited
 * Author URI:      https://www.itineris.co.uk/
 * Text Domain:     page-as-post-type-archive
 */

namespace Itineris\PageAsPostTypeArchive;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

define('ITINERIS_PAPTA_SLUG', 'page-as-post-type-archive');
define('ITINERIS_PAPTA_DIR', untrailingslashit(plugin_dir_path(__FILE__)));

$composer = __DIR__ . '/vendor/autoload.php';
if (! file_exists($composer)) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'page-as-post-type-archive'));
}

require_once $composer;
require_once ITINERIS_PAPTA_DIR . '/src/helpers.php';

CustomPages::instance();
