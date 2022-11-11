<?php

declare(strict_types=1);

namespace Itineris\PageAsPostTypeArchive;

use Roots\Acorn\ServiceProvider;

class ArchiveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'ItinerisPageAsPostTypeArchive');

        $this->publishes([
            __DIR__ . '/resources/views' => $this->app->resourcePath('views/page-as-post-type-archive'),
        ], 'ItinerisPageAsPostTypeArchive');
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('ItinerisPageAsPostTypeArchive', function () {
            return new ItinerisPageAsPostTypeArchive();
        });

        return $this->app->make('ItinerisPageAsPostTypeArchive');
    }
}
