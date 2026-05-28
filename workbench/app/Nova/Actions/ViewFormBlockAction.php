<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class ViewFormBlockAction extends Action
{
    public $name = 'View form block';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::stack([
            Block::heading('Form block')->small(),
            Block::text('Renders the target action\'s fields inline. Submitting dispatches through the same intercept core as the action block.'),
            Block::form(DemoFormSubmitAction::class),
        ])
            ->title('Demo — form block')
            ->closeButton('Cancel');
    }
}
