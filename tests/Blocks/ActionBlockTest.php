<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use LogicException;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\ActionBlock;
use Markwalet\NovaModalResponse\Blocks\Inlineable;
use Markwalet\NovaModalResponse\Tests\TestCase;
use stdClass;

class ActionBlockTest extends TestCase
{
    public function test_factory_with_only_action_class_returns_an_action_block(): void
    {
        $block = Block::action(FakeFieldlessAction::class);

        $this->assertInstanceOf(ActionBlock::class, $block);
    }

    public function test_factory_with_label_and_action_class_returns_an_action_block(): void
    {
        $block = Block::action('Run it', FakeFieldlessAction::class);

        $this->assertInstanceOf(ActionBlock::class, $block);
    }

    public function test_action_block_is_an_inline_atom(): void
    {
        $this->assertInstanceOf(Inlineable::class, Block::action(FakeFieldlessAction::class));
    }

    public function test_label_defaults_to_the_actions_name(): void
    {
        $block = Block::action(FakeFieldlessAction::class);

        $this->assertSame('Fake fieldless', $block->toArray()['value']);
    }

    public function test_explicit_label_overrides_the_actions_name(): void
    {
        $block = Block::action('Custom label', FakeFieldlessAction::class);

        $this->assertSame('Custom label', $block->toArray()['value']);
    }

    public function test_serialization_carries_type_and_uri_key(): void
    {
        $payload = Block::action(FakeFieldlessAction::class)->toArray();

        $this->assertSame('action', $payload['type']);
        $this->assertSame('fake-fieldless', $payload['action']);
    }

    public function test_serialization_defaults_reload_to_true(): void
    {
        $payload = Block::action(FakeFieldlessAction::class)->toArray();

        $this->assertTrue($payload['reload']);
    }

    public function test_without_reload_flips_the_reload_flag(): void
    {
        $payload = Block::action(FakeFieldlessAction::class)->withoutReload()->toArray();

        $this->assertFalse($payload['reload']);
    }

    public function test_without_reload_returns_the_block_for_chaining(): void
    {
        $block = Block::action(FakeFieldlessAction::class);

        $this->assertSame($block, $block->withoutReload());
    }

    public function test_serialization_omits_disposition_and_stay_open_fields(): void
    {
        $payload = Block::action(FakeFieldlessAction::class)->toArray();

        $this->assertArrayNotHasKey('disposition', $payload);
        $this->assertArrayNotHasKey('stayOpen', $payload);
    }

    public function test_serialization_includes_origin_context_keys(): void
    {
        $payload = Block::action(FakeFieldlessAction::class)->toArray();

        $this->assertArrayHasKey('resourceName', $payload);
        $this->assertArrayHasKey('resources', $payload);
        $this->assertSame([], $payload['resources']);
    }

    public function test_serialization_captures_origin_context_from_the_active_request(): void
    {
        $request = Request::create('/nova-api/users/action', 'POST', ['resources' => ['1', '2']]);
        $request->setRouteResolver(function () {
            $route = new Route('POST', '/nova-api/{resource}/action', []);
            $route->bind(Request::create('/nova-api/users/action'));
            $route->setParameter('resource', 'users');

            return $route;
        });

        $this->app->instance('request', $request);

        $payload = Block::action(FakeFieldlessAction::class)->toArray();

        $this->assertSame('users', $payload['resourceName']);
        $this->assertSame(['1', '2'], $payload['resources']);
    }

    public function test_confirmation_defaults_to_the_actions_confirm_text(): void
    {
        $payload = Block::action(FakeFieldlessAction::class)->toArray();

        $this->assertSame('Are you sure you want to run this action?', $payload['confirmText']);
    }

    public function test_confirmation_honors_the_actions_custom_confirm_text(): void
    {
        $payload = Block::action(FakeConfirmingAction::class)->toArray();

        $this->assertSame('Really wipe everything?', $payload['confirmText']);
    }

    public function test_no_confirmation_when_action_disables_it(): void
    {
        $payload = Block::action(FakeSilentAction::class)->toArray();

        $this->assertNull($payload['confirmText']);
    }

    public function test_confirm_method_with_custom_text_forces_a_prompt(): void
    {
        $payload = Block::action(FakeSilentAction::class)
            ->confirm('Are you sure?')
            ->toArray();

        $this->assertSame('Are you sure?', $payload['confirmText']);
    }

    public function test_confirm_method_without_arguments_uses_the_actions_text(): void
    {
        $payload = Block::action(FakeConfirmingAction::class)
            ->confirm()
            ->toArray();

        $this->assertSame('Really wipe everything?', $payload['confirmText']);
    }

    public function test_confirm_method_overrides_an_action_that_opts_out(): void
    {
        $payload = Block::action(FakeSilentAction::class)
            ->confirm()
            ->toArray();

        // Action sets withoutConfirmation = true, but the block forces a
        // prompt and falls back to Nova's default text.
        $this->assertSame('Are you sure you want to run this action?', $payload['confirmText']);
    }

    public function test_serialization_throws_when_target_action_declares_fields(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Block::form');

        Block::action(FakeFieldfulAction::class)->toArray();
    }

    public function test_factory_rejects_non_action_class(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Block::action(stdClass::class);
    }

    public function test_an_action_is_accepted_inside_an_inline_group(): void
    {
        $block = Block::inline([
            Block::text('Status:'),
            Block::action(FakeFieldlessAction::class),
        ]);

        $serialized = $block->toArray();

        $this->assertSame('inline', $serialized['type']);
        $this->assertCount(2, $serialized['value']);
        $this->assertSame('action', $serialized['value'][1]['type']);
        $this->assertSame('Fake fieldless', $serialized['value'][1]['value']);
    }
}

class FakeFieldlessAction extends Action
{
    public $name = 'Fake fieldless';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        return ActionResponse::message('ok');
    }
}

class FakeConfirmingAction extends Action
{
    public $name = 'Fake confirming';

    public $confirmText = 'Really wipe everything?';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        return ActionResponse::message('ok');
    }
}

class FakeSilentAction extends Action
{
    public $name = 'Fake silent';

    public $withoutConfirmation = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        return ActionResponse::message('ok');
    }
}

class FakeFieldfulAction extends Action
{
    public $name = 'Fake fieldful';

    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Comment'),
        ];
    }

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        return ActionResponse::message('ok');
    }
}
