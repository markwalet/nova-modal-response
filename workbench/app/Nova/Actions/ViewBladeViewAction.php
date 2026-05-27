<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\ModalResponse;

class ViewBladeViewAction extends Action
{
    public $name = 'View blade view';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::view('workbench::demo-block', [
            'title' => 'A whole modal body from one Blade view',
            'body' => 'ModalResponse::view() renders the named view server-side and wraps it in a single-block stack.',
        ])
            ->title('Demo — view() top-level sugar')
            ->closeButton('Close');
    }
}
