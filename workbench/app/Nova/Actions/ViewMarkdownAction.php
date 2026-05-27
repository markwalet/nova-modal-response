<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

use function Orchestra\Testbench\workbench_path;

class ViewMarkdownAction extends Action
{
    public $name = 'View markdown';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::stack([
            Block::heading('Inline markdown')->small(),
            Block::markdown(<<<'MD'
                # Release notes

                A markdown block compiles to an `html` block at serialize time.

                - GitHub-flavored markdown via `Str::markdown()`
                - **Bold**, *italic*, and `inline code`
                - [Links](https://nova.laravel.com)

                > Trusted-input model — same as the html block.
                MD),
            Block::divider(),
            Block::heading('Markdown loaded from a file')->small(),
            // testbench does not resolve this package's base_path(), so the demo
            // fixture lives in the workbench and is loaded via its real path.
            Block::markdown(workbench_path('resources/demo.md'))->file(),
        ])
            ->title('Demo — markdown block')
            ->closeButton('Close');
    }
}
