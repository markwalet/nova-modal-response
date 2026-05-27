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
            $dist = __DIR__.'/../dist';

            // `mix-manifest.json` records the most recent build, so serving the
            // assets it names means `npm run prod` wins even when a stale
            // unminified dev build is still on disk, and `npm run watch` wins
            // while it's running. The manifest is gitignored, so a consumer
            // install never has one — there we fall back to the shipped minified
            // bundle. See docs/adr/0005-dev-asset-builds-isolated-by-filename.md.
            if (is_file($dist.'/mix-manifest.json')) {
                Nova::mix('nova-modal-response', $dist);

                return;
            }

            Nova::script('nova-modal-response', $dist.'/js/asset.min.js');
            Nova::style('nova-modal-response', $dist.'/css/asset.min.css');
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
