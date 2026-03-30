<?php

namespace Markwalet\NovaModalResponse;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-modal-response', __DIR__.'/../dist/js/asset.js');
            Nova::style('nova-modal-response', __DIR__.'/../dist/css/asset.css');
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
