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
            Nova::script('nova-modal-response', $this->asset('js/asset', 'js'));
            Nova::style('nova-modal-response', $this->asset('css/asset', 'css'));
        });
    }

    /**
     * Resolve a compiled asset path, preferring the unminified dev build
     * (from `npm run watch` / `composer serve`) when present, otherwise the
     * minified bundle shipped with the package.
     *
     * See docs/adr/0005-dev-asset-builds-isolated-by-filename.md.
     */
    protected function asset(string $file, string $extension): string
    {
        $dev = __DIR__."/../dist/{$file}.{$extension}";

        return is_file($dev) ? $dev : __DIR__."/../dist/{$file}.min.{$extension}";
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
