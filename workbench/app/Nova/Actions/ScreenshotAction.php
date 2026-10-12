<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

/**
 * Deterministic modals for the README screenshots (`npm run screenshots`).
 * Only registered when the workbench runs with NMR_SCREENSHOTS=1, so they
 * stay out of the showcase menu. No live data, dates or timings.
 */
class ScreenshotAction extends Action
{
    public const SCENES = ['hero', 'text', 'code', 'inline', 'containers', 'one-liner'];

    public $withoutConfirmation = true;

    public $showInline = true;

    public function __construct(private readonly string $scene)
    {
        if (! in_array($scene, self::SCENES, true)) {
            throw new InvalidArgumentException("Unknown screenshot scene [{$scene}].");
        }

        $this->name = "Screenshot: {$scene}";
    }

    public function uriKey(): string
    {
        return "screenshot-{$this->scene}";
    }

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        return match ($this->scene) {
            'hero' => $this->hero(),
            'text' => $this->text(),
            'code' => $this->code(),
            'inline' => $this->inline(),
            'containers' => $this->containers(),
            'one-liner' => $this->oneLiner(),
        };
    }

    private function hero(): ModalResponse
    {
        // The hero shows the code that builds it: keep $source in sync with the return below.
        $source = <<<'PHP'
            return ModalResponse::stack([
                Block::heading('Deploy #1284'),
                Block::inline([
                    Block::icon('check-circle')->success(),
                    'Succeeded',
                    Block::badge('production')->info(),
                ]),
                Block::inline(['Duration', Block::badge('42s')])->spread(),
                Block::code($source)->language('php'),
            ])->title('Deployment summary');
            PHP;

        return ModalResponse::stack([
            Block::heading('Deploy #1284'),
            Block::inline([
                Block::icon('check-circle')->success(),
                'Succeeded',
                Block::badge('production')->info(),
            ]),
            Block::inline(['Duration', Block::badge('42s')])->spread(),
            Block::code($source)->language('php'),
        ])->title('Deployment summary');
    }

    private function text(): ModalResponse
    {
        return ModalResponse::stack([
            Block::heading('Import finished')->large(),
            Block::text('Processed 1,204 rows from customers.csv. Three rows were skipped and need a manual fix.'),
            Block::divider(),
            Block::heading('Skipped rows')->small(),
            Block::list([
                'Row 18: missing email address',
                'Row 211: duplicate customer number',
                'Row 940: unknown country code "UK"',
            ]),
            Block::heading('Next steps')->small(),
            Block::list([
                'Correct the rows in the source file',
                'Re-run the import with "Only failed rows"',
            ])->ordered(),
        ])->title('Customer import')->size('xl');
    }

    private function code(): ModalResponse
    {
        return ModalResponse::stack([
            Block::heading('Query')->small(),
            Block::code(<<<'SQL'
                SELECT id, email, created_at
                FROM customers
                WHERE country = 'GB' AND deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT 1;
                SQL)->language('sql'),
            Block::heading('Result')->small(),
            Block::json([
                'id' => 5521,
                'email' => 'ada@example.com',
                'created_at' => '2026-03-14T10:22:05+00:00',
            ]),
            Block::markdown('Ran in **4 ms** on the read replica. See [query logging](https://example.com/docs/queries) for details.'),
        ])->title('Query preview')->size('xl');
    }

    private function inline(): ModalResponse
    {
        return ModalResponse::stack([
            Block::inline([
                Block::icon('check-circle')->success()->large(),
                'Paid with Visa',
                Block::badge('•••• 4242'),
            ]),
            Block::inline(['Fulfilment', Block::badge('Shipped')->success()->icon('truck')])->spread(),
            Block::inline(['Priority', Block::badge('High')->warning()])->spread(),
            Block::inline(['Fraud check', Block::badge('Review')->danger()->icon('exclamation-triangle')])->spread(),
            Block::divider(),
            Block::inline([
                Block::badge('B2B'),
                Block::badge('Returning')->info(),
                Block::badge('Verified')->success(),
                Block::badge('Backorder')->warning(),
                Block::badge('Chargeback risk')->danger(),
            ]),
            Block::divider(),
            Block::inline([
                Block::link('Open in Stripe', 'https://example.com')
                    ->button()
                    ->newTab()
                    ->icon('arrow-top-right-on-square', trailing: true),
                Block::link('Download invoice', 'https://example.com')->icon('arrow-down-tray'),
                Block::link('Customer profile', 'https://example.com'),
            ]),
        ])->title('Order #10421')->size('xl');
    }

    private function containers(): ModalResponse
    {
        return ModalResponse::tabs([
            'Overview' => [
                Block::inline(['Customer', 'Ada Lovelace'])->spread(),
                Block::inline(['Lifetime value', Block::badge('€ 4,210')->success()])->spread(),
                Block::inline(['Open tickets', Block::badge('2')->warning()])->spread(),
                Block::collapsible('Billing address', Block::json([
                    'street' => '12 St James\'s Square',
                    'city' => 'London',
                    'postal_code' => 'SW1Y 4LB',
                    'country' => 'GB',
                ]))->expanded(),
                Block::collapsible('Internal notes', 'Prefers invoices by post.'),
            ],
            'Orders' => [
                Block::list(['#10421 — € 129.95', '#10388 — € 54.00', '#10102 — € 310.50']),
            ],
            'Activity' => [
                Block::text('Last login 3 days ago.'),
            ],
        ])->title('Customer #5521')->size('xl');
    }

    private function oneLiner(): ModalResponse
    {
        return ModalResponse::json([
            'event' => 'order.paid',
            'occurred_at' => '2026-10-12T09:41:00+00:00',
            'data' => [
                'order' => 10421,
                'amount' => 12995,
                'currency' => 'EUR',
                'customer' => ['id' => 5521, 'email' => 'ada@example.com'],
            ],
        ])->title('Webhook payload')->size('xl');
    }
}
