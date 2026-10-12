<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;
use Throwable;

class ApiProbeAction extends Action
{
    private const ENDPOINTS = [
        'ok' => 'https://jsonplaceholder.typicode.com/todos?_page=1&_limit=3',
        'not-found' => 'https://jsonplaceholder.typicode.com/todos/0',
        'unreachable' => 'https://api.example.invalid/todos',
    ];

    public $name = 'API probe';

    public $showInline = true;

    public $confirmButtonText = 'Probe';

    public function fields(NovaRequest $request): array
    {
        return [
            Select::make('Scenario')
                ->options([
                    'ok' => 'Successful request',
                    'not-found' => '404 response',
                    'unreachable' => 'Unreachable host',
                ])
                ->default('ok'),
        ];
    }

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        $endpoint = self::ENDPOINTS[$fields->get('scenario')] ?? self::ENDPOINTS['ok'];

        $start = microtime(true);

        try {
            $response = Http::timeout(5)->get($endpoint);

            $ok = $response->successful();
            $status = $response->status();
            $total = $response->header('X-Total-Count') ?: null;
            $body = $response->json() ?? ['body' => $response->body()];
        } catch (Throwable $e) {
            $ok = false;
            $status = 0;
            $total = null;
            $body = ['error' => $e->getMessage()];
        }

        $ms = (int) round((microtime(true) - $start) * 1000);

        $statusRow = [
            $ok
                ? Block::badge("GET {$status}")->success()->icon('check-circle')
                : Block::badge($status > 0 ? "GET {$status}" : 'GET failed')->danger()->icon('x-circle'),
            Block::badge("{$ms} ms")->info(),
        ];

        if ($total !== null) {
            $statusRow[] = Block::badge(count($body).' of '.$total);
        }

        return ModalResponse::stack([
            Block::inline($statusRow),
            Block::text($endpoint),
            Block::heading($ok ? 'Response body' : 'Error')->small(),
            Block::json($body),
            Block::inline([
                Block::link('API docs', 'https://jsonplaceholder.typicode.com/guide/')
                    ->newTab()
                    ->icon('arrow-top-right-on-square', trailing: true),
            ]),
        ])
            ->title('Demo — API probe')
            ->size('2xl')
            ->closeButton('Close');
    }
}
