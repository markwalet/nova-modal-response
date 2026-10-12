<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\ModalResponse;
use Workbench\App\Models\User;

/**
 * The most common real-world use: a v1-style one-liner previewing an outbound
 * payload. Works unchanged in v2 and renders edge-to-edge as a single block.
 */
class PreviewPayloadAction extends Action
{
    public $name = 'Preview webhook payload';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        /** @var User $user */
        $user = $models->first();

        return ModalResponse::json([
            'event' => 'user.updated',
            'occurred_at' => now()->toIso8601String(),
            'data' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'verified' => $user->email_verified_at !== null,
            ],
        ])->title('Demo — webhook payload');
    }
}
