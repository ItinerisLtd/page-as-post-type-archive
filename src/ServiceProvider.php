<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive;

use Roots\Acorn\ServiceProvider as RootsServiceProvider;
use function Roots\base_path;

class ServiceProvider extends RootsServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(
            base_path('resources/views'),
            'Theme'
        );

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views/',
            'ItinerisPageAsPostTypeArchive'
        );

        // TODO: add View Composer here.
    }
}
