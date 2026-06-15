<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class DeploymentSummaryAction extends Action
{
    public $name = 'Deployment summary';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        $log = <<<'LOG'
            [12:04:01] Fetching dependencies...
            [12:04:09] Running migrations (3 ran)
            [12:04:12] Compiling assets...
            [12:04:31] Restarting queue workers
            [12:04:32] Deploy finished in 42s
            LOG;

        return ModalResponse::stack([
            Block::heading('Deployment summary'),
            Block::inline([
                Block::icon('check-circle')->success(),
                Block::text('Deploy #1284 succeeded'),
                Block::badge('production')->info(),
            ]),
            Block::inline([
                Block::text('Duration'),
                Block::badge('42s'),
            ])->spread(),
            Block::inline([
                Block::text('Commit'),
                Block::badge('a1b2c3d'),
            ])->spread(),
            Block::divider(),
            Block::heading('Steps')->small(),
            Block::list([
                'Install dependencies',
                'Run migrations',
                'Compile assets',
                'Restart workers',
            ])->ordered(),
            Block::collapsible('Build log', [
                Block::code($log)->withoutHighlighting(),
            ]),
            Block::divider(),
            Block::inline([
                Block::link('View changelog', 'https://github.com/markwalet/nova-modal-response/blob/main/CHANGELOG.md')
                    ->button()
                    ->newTab()
                    ->icon('arrow-top-right-on-square', trailing: true),
            ]),
        ])
            ->title('Demo — deployment summary')
            ->closeButton('Close');
    }
}
