<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class ViewActionBlockAction extends Action
{
    public $name = 'View action block';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::stack([
            Block::heading('Non-modal target')->small(),
            Block::text('Dispatches a fieldless action that returns a toast. Parent modal closes by default.'),
            Block::action('Run toast action', DemoToastAction::class),
            Block::heading('Non-modal target — stayOpen')->small(),
            Block::text('Same toast action, but parent modal stays open.'),
            Block::action('Run toast (stay open)', DemoToastAction::class)->stayOpen(),
            Block::divider(),
            Block::heading('Modal target')->small(),
            Block::text('Dispatches a fieldless action that returns another ModalResponse — stacks as a child modal.'),
            Block::action('Open child modal', DemoChildModalAction::class),
        ])
            ->title('Demo — action block')
            ->closeButton('Close');
    }
}
