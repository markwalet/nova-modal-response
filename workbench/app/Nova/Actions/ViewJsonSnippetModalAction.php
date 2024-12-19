<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonException;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\ActionModal;

class ViewJsonSnippetModalAction extends Action
{
    public $name = 'Show JSON snippet';
    public $withoutConfirmation = true;
    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        $data = [
            'lorem' => 'ipsum',
            'dolor' => [
                'sit',
                'amet',
            ],
        ];

        return ActionModal::json($data)
            ->title('JSON Snippet')
            ->withoutSyntaxHighlighting()
            ->closeButton('I\'ve seen enough!');
    }
}
