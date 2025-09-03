<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\ModalResponse;
use Random\RandomException;

class ExecuteUpdateAction extends Action
{
    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Execute Update action';

    /**
     * Determine where the action redirection should be without confirmation.
     *
     * @var bool
     */
    public $withoutConfirmation = true;

    /**
     * Indicates if this action is available on the resource's table row.
     *
     * @var bool
     */
    public $showInline = true;

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection<int, Model> $models
     * @return Action|ActionResponse
     * @throws RandomException
     */
    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        file_put_contents(__DIR__.'/../../../storage/metric.txt', random_int(1000, 9999));

        return ModalResponse::text('When closing the modal, the metrics should be automatically updated');
    }
}
