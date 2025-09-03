<?php

namespace Markwalet\NovaModalResponse;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-modal-response', __DIR__.'/../dist/js/asset.js');
            Nova::style('nova-modal-response', __DIR__.'/../dist/css/asset.css');

            Nova::provideToScript([
                'nova_modal_response' => Config::get(
                    'nova-modal-response',
                    [
                        'emit_action_executed_on_modal_mounted' => false,
                        'emit_action_executed_on_modal_close' => false,
                    ],
                ),
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Config
        if (false === $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../config/nova-modal-response.php',
                'nova-modal-response',
            );
        }
    }
}
