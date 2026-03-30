<?php

namespace Markwalet\NovaModalResponse\Tests;

use Markwalet\NovaModalResponse\AssetServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            AssetServiceProvider::class,
        ];
    }
}
