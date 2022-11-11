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
            __DIR__ . '/resources/views' => $this->app->resourcePath('views/components'),
        ], 'ItinerisPageAsPostTypeArchive');
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('ItinerisPageAsPostTypeArchive', fn (): CustomPages => new CustomPages());

        return $this->app->make('ItinerisPageAsPostTypeArchive');
    }
}
