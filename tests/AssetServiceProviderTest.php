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

        $this->assertCount(1, Nova::allScripts());
        $this->assertCount(1, Nova::allStyles());
        $this->assertSame('nova-modal-response', Nova::allScripts()[0]->name());
        $this->assertSame($packageRoot.'/dist/js/asset.js', realpath(Nova::allScripts()[0]->path()));
        $this->assertSame('nova-modal-response', Nova::allStyles()[0]->name());
        $this->assertSame($packageRoot.'/dist/css/asset.css', realpath(Nova::allStyles()[0]->path()));
    }
}
