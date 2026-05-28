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
        return ModalResponse::stack([
            Block::heading('String-keyed shorthand')->small(),
            Block::tabs([
                'Overview' => [
                    Block::heading('Overview tab'),
                    Block::text('Tabs accept a "label => [blocks]" shorthand.'),
                    Block::badge('Default')->info(),
                ],
                'Details' => [
                    Block::heading('Details tab'),
                    Block::list(['Item one', 'Item two', 'Item three']),
                ],
                'Activity' => [
                    Block::heading('Activity tab'),
                    Block::code("git log --oneline -5"),
                ],
            ]),
            Block::divider(),
            Block::heading('Explicit Block::tab() with active default')->small(),
            Block::tabs([
                Block::tab('Draft', [
                    Block::text('Draft state.'),
                    Block::badge('Draft')->warning(),
                ]),
                Block::tab('Published', [
                    Block::text('Published state — default active.'),
                    Block::badge('Published')->success(),
                ])->active(),
                Block::tab('Archived', [
                    Block::text('Archived state.'),
                    Block::badge('Archived')->danger(),
                ]),
            ]),
        ])
            ->title('Demo — tabs block')
            ->closeButton('Close');
    }
}
