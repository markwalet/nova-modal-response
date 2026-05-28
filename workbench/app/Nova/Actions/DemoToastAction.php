<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

class DemoToastAction extends Action
{
    public $name = 'Demo toast (non-modal target)';

    public $withoutConfirmation = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return Action::message('Toast from a dispatched action block.');
    }
}
