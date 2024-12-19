<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\ModalResponse;

class ViewCodeSnippetAction extends Action
{
    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'View PHP snippet';

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
     */
    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::code(file_get_contents(__FILE__))
            ->title('Look at me!')
            ->closeButton('I\'ve seen enough');
    }
}
