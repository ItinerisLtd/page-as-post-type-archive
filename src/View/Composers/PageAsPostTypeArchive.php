<?php

declare(strict_types=1);

namespace Itineris\SageFLBuilder\View\Composers;

use Roots\Acorn\View\Composer;

class PageAsPostTypeArchive extends Composer
{
    /**
     * List of views served by this composer.
     */
    protected static $views = [
        'ItinerisPageAsPostTypeArchive::default',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        dd('post_id');
        return [
            'post_id' => 123123,
        ];
    }
}
