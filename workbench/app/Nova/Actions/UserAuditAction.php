<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;
use Workbench\App\Models\User;

class UserAuditAction extends Action
{
    public $name = 'User audit';

    public $withoutConfirmation = true;

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): Action|ActionResponse
    {
        /** @var User $user */
        $user = $models->first();

        $verified = $user->email_verified_at !== null;

        return ModalResponse::tabs([
            'Overview' => [
                Block::inline([
                    Block::text('Name'),
                    Block::text($user->name),
                ])->spread(),
                Block::inline([
                    Block::text('Email'),
                    Block::text($user->email),
                ])->spread(),
                Block::inline([
                    Block::text('Status'),
                    $verified
                        ? Block::badge('Verified')->success()->icon('check-circle')
                        : Block::badge('Unverified')->warning()->icon('exclamation-triangle'),
                ])->spread(),
                Block::inline([
                    Block::text('ID'),
                    Block::badge((string) $user->getKey()),
                ])->spread(),
            ],
            'Roles' => [
                Block::inline([
                    Block::badge('Admin')->success()->icon('shield-check'),
                    Block::badge('Editor')->info()->icon('pencil-square'),
                ]),
            ],
            'Raw' => [
                Block::json($user->toArray()),
            ],
        ])
            ->title('Demo — user audit')
            ->size('2xl')
            ->closeButton('Close');
    }
}
