<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class ViewTabsAction extends Action
{
    public $name = 'View tabs';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::tabs([
            'Overview' => [
                Block::heading('Overview'),
                Block::text('Tabs accept a "label => [blocks]" shorthand.'),
                Block::badge('Default')->info(),
            ],
            'Details' => [
                Block::heading('Details'),
                Block::list(['Item one', 'Item two', 'Item three']),
            ],
            Block::tab('Activity', [
                Block::heading('Activity'),
                Block::code('git log --oneline -5'),
            ])->active(),
            'Archived' => [
                Block::heading('Archived'),
                Block::badge('Archived')->danger(),
            ],
        ])
            ->title('Demo — tabs block')
            ->closeButton('Close');
    }
}
