<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class ViewWithoutHighlightingAction extends Action
{
    public $name = 'View without highlighting';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return ModalResponse::stack([
            Block::text('Both code and JSON below should render as plaintext (no syntax colouring), driven by the modal-level withoutSyntaxHighlighting() helper:'),
            Block::heading('Code block')->small(),
            Block::code("<?php\n\nfunction greet(string \$name): string {\n    return \"Hello, {\$name}!\";\n}"),
            Block::heading('JSON block')->small(),
            Block::json([
                'ok' => true,
                'imported' => 42,
                'meta' => ['duration_ms' => 137],
            ]),
            Block::divider(),
            Block::text('The text/heading blocks above are not affected — the bulk helper only touches code and json.'),
        ])
            ->title('Demo — withoutSyntaxHighlighting() bulk helper')
            ->closeButton('Close')
            ->withoutSyntaxHighlighting();
    }
}
