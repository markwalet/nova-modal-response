<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class ViewTextStackAction extends Action
{
    public $name = 'View text stack';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::stack([Block::text('hi')])
            ->title('Demo')
            ->closeButton('Close');
    }
}
