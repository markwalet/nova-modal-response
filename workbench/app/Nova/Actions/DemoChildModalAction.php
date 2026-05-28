<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class DemoChildModalAction extends Action
{
    public $name = 'Demo child modal (modal target)';

    public $withoutConfirmation = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::stack([
            Block::heading('Child modal'),
            Block::text('Dispatched from an action block — stacked over the parent modal.'),
            Block::badge('Stacked')->success(),
        ])
            ->title('Child modal')
            ->closeButton('Close');
    }
}
