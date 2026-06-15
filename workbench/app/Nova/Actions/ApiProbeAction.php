<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;
use Throwable;

class ApiProbeAction extends Action
{
    public $name = 'API probe';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        $endpoint = 'https://jsonplaceholder.typicode.com/todos?_page=1&_limit=3';

        $start = microtime(true);

        try {
            $response = Http::timeout(5)->get($endpoint);

            $ok = $response->successful();
            $status = $response->status();
            $total = $response->header('X-Total-Count');
            $body = $response->json();
        } catch (Throwable $e) {
            $ok = false;
            $status = 0;
            $total = null;
            $body = ['error' => $e->getMessage()];
        }

        $ms = (int) round((microtime(true) - $start) * 1000);

        $statusRow = Block::inline([
            Block::text($ok ? 'Request succeeded' : 'Request failed'),
            $ok
                ? Block::badge("GET {$status}")->success()->icon('check-circle')
                : Block::badge($status > 0 ? "GET {$status}" : 'GET failed')->danger()->icon('x-circle'),
        ]);

        $blocks = [
            Block::heading('API probe'),
            Block::text('Live GET against the JSONPlaceholder todos endpoint.'),
            $statusRow,
            Block::inline([
                Block::text('Response time'),
                Block::badge("{$ms} ms")->info(),
            ]),
        ];

        if ($total !== null) {
            $blocks[] = Block::inline([
                Block::text('Results'),
                Block::badge(count($body).' of '.$total),
            ]);
        }

        $blocks[] = Block::divider();
        $blocks[] = Block::heading('Response body')->small();
        $blocks[] = Block::json($body);
        $blocks[] = Block::inline([
            Block::link('API docs', 'https://jsonplaceholder.typicode.com/guide/')
                ->newTab()
                ->icon('arrow-top-right-on-square', trailing: true),
        ]);

        return ModalResponse::stack($blocks)
            ->title('Demo — API probe')
            ->size('2xl')
            ->closeButton('Close');
    }
}
