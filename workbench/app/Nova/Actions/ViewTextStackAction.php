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
        return ModalResponse::stack([
            Block::heading('Large heading')->large(),
            Block::heading('Medium heading (default)'),
            Block::heading('Small heading')->small(),
            Block::text('A paragraph above some raw HTML:'),
            Block::html('<p><b>Bold</b> and <i>italic</i> and a <a href="#" style="color:#06f">link</a> — rendered via <code>v-html</code>.</p>'),
            Block::divider(),
            Block::heading('Unordered list')->small(),
            Block::list(['apples', 'oranges', 'pears']),
            Block::heading('Ordered list')->small(),
            Block::list(['first', 'second', 'third'])->ordered(),
            Block::divider(),
            Block::heading('Badge variants')->small(),
            Block::badge('Draft'),
            Block::badge('Pending review')->info(),
            Block::badge('Published')->success(),
            Block::badge('Deprecated')->warning(),
            Block::badge('Archived')->danger(),
            Block::divider(),
            Block::heading('Inline group — packed (default)')->small(),
            Block::inline([
                Block::text('Status:'),
                Block::badge('Published')->success(),
            ]),
            Block::heading('Inline group — spread (key/value row)')->small(),
            Block::inline([
                Block::text('Deployment'),
                Block::badge('Live')->success(),
            ])->spread(),
            Block::divider(),
            Block::heading('Code — autodetect')->small(),
            Block::code("<?php\n\nfunction greet(string \$name): string {\n    return \"Hello, {\$name}!\";\n}"),
            Block::heading('Code — explicit language (sql)')->small(),
            Block::code('SELECT id, email FROM users WHERE active = 1 ORDER BY created_at DESC LIMIT 10;')->language('sql'),
            Block::heading('Code — plaintext (no highlighting)')->small(),
            Block::code("2026-05-26 18:30:11  INFO  job finished in 42ms\n2026-05-26 18:30:12  WARN  retrying upstream call")->withoutHighlighting(),
            Block::divider(),
            Block::heading('JSON payload')->small(),
            Block::json([
                'ok' => true,
                'imported' => 42,
                'failures' => [],
                'meta' => ['duration_ms' => 137, 'source' => 'csv'],
            ]),
        ])
            ->title('Demo — all v2 blocks')
            ->closeButton('Close');
    }
}
