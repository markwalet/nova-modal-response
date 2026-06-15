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

        return ModalResponse::tabs([
            'Overview' => [
                Block::heading('Overview'),
                Block::inline([
                    Block::text('Name'),
                    Block::badge($user->name),
                ])->spread(),
                Block::inline([
                    Block::text('Email'),
                    Block::badge($user->email),
                ])->spread(),
                Block::inline([
                    Block::text('ID'),
                    Block::badge((string) $user->getKey()),
                ])->spread(),
            ],
            'Roles' => [
                Block::heading('Roles'),
                Block::inline([
                    Block::badge('Admin')->success()->icon('shield-check'),
                    Block::badge('Editor')->info()->icon('pencil-square'),
                    Block::badge('Suspended')->danger()->icon('no-symbol'),
                ]),
            ],
            'Raw' => [
                Block::heading('Raw record'),
                Block::json($user->toArray()),
            ],
        ])
            ->title('Demo — user audit')
            ->size('2xl')
            ->closeButton('Close');
    }
}
