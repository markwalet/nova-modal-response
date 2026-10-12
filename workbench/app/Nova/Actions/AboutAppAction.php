<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Nova;
use Markwalet\NovaModalResponse\ModalResponse;

class AboutAppAction extends Action
{
    public $name = 'About this app';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::view('workbench::about', [
            'php' => PHP_VERSION,
            'laravel' => app()->version(),
            'nova' => Nova::version(),
        ])
            ->title('Demo — about this app')
            ->closeButton('Close');
    }
}
