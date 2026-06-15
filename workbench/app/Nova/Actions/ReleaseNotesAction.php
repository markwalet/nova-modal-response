<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

use function Orchestra\Testbench\package_path;

class ReleaseNotesAction extends Action
{
    public $name = 'Release notes';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        // Renders the package's real CHANGELOG.md from disk at serialize time.
        return ModalResponse::stack([
            Block::markdown(package_path('CHANGELOG.md'))->file(),
        ])
            ->title('Demo — release notes')
            ->size('2xl')
            ->closeButton('Close');
    }
}
