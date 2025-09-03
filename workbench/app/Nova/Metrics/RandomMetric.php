<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class RandomMetric extends Trend
{
    /**
     * Calculate the value of the metric.
     */
    public function calculate(NovaRequest $request): TrendResult
    {
        $path = __DIR__.'/../../../storage/metric.txt';
        $last = file_exists($path) ? (int) file_get_contents($path) : 5000;

        return (new TrendResult)->trend([
            'July 1' => 2500,
            'July 2' => 7500,
            'July 3' => 5000,
            'July 4' => $last,
        ])->showLatestValue();
    }
}