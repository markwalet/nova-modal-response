<?php

namespace Markwalet\NovaModalResponse\Tests;

use Illuminate\Http\Request;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class AssetServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Nova::$scripts = [];
        Nova::$styles = [];
    }

    public function test_assets_are_registered_when_nova_is_serving(): void
    {
        ServingNova::dispatch($this->app, Request::create('/nova', 'GET'));

        $packageRoot = dirname(__DIR__);

        // The package always ships the minified bundle. The manifest and any
        // unminified dev build are gitignored, so they only exist in a local
        // checkout that has run a build.
        $this->assertFileExists($packageRoot.'/dist/js/asset.min.js');
        $this->assertFileExists($packageRoot.'/dist/css/asset.min.css');

        $scripts = Nova::allScripts();
        $styles = Nova::allStyles();

        $this->assertCount(1, $scripts);
        $this->assertCount(1, $styles);

        // A consumer install (no manifest) registers under the bare package
        // name; a local build registers via Nova::mix(), which appends a hash.
        $this->assertStringStartsWith('nova-modal-response', $scripts[0]->name());
        $this->assertStringStartsWith('nova-modal-response', $styles[0]->name());

        // Whichever build is current, the registered file must be the matching
        // dist asset (minified or dev) and must actually exist on disk.
        $this->assertMatchesRegularExpression('#/dist/js/asset(\.min)?\.js$#', $scripts[0]->path());
        $this->assertMatchesRegularExpression('#/dist/css/asset(\.min)?\.css$#', $styles[0]->path());
        $this->assertFileExists($scripts[0]->path());
        $this->assertFileExists($styles[0]->path());
    }
}
