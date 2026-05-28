<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class DemoFormSubmitAction extends Action
{
    public $name = 'Demo form submit';

    public $confirmButtonText = 'Submit feedback';

    public function fields(NovaRequest $request): array
    {
        return [
            Select::make('Mood')
                ->options([
                    'happy' => 'Happy',
                    'meh' => 'Meh',
                    'sad' => 'Sad',
                ])
                ->rules('required'),

            Text::make('Comment')
                ->rules('required', 'min:3'),
        ];
    }

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::stack([
            Block::heading('Thanks!')->small(),
            Block::text("Mood: {$fields->mood}"),
            Block::text("Comment: {$fields->comment}"),
        ])
            ->title('Form submitted')
            ->closeButton('Close');
    }
}
