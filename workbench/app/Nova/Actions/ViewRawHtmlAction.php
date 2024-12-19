<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\ActionModal;

class ViewRawHtmlAction extends Action
{
    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Show Rendered HTML';

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
        return ActionModal::html(<<<HTML
<p class="py-3 text-lg"><b>Lorem ipsum dolor sit amet,</b> consectetur adipisicing elit. Amet autem debitis illum nostrum repellendus suscipit, vitae. Deleniti error, esse eum illum nisi numquam pariatur perspiciatis, ratione recusandae repellat, vel voluptates.</p>
<p>Lorem ipsum dolor sit amet, <b>consectetur</b> adipisicing elit. Autem consectetur cupiditate delectus ducimus eligendi enim esse fuga hic laudantium, nisi quae qui quis quod ratione repellat. Consequuntur provident suscipit voluptatibus!</p>
HTML)->title('HTML');
    }
}
