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
use Markwalet\NovaModalResponse\Blocks\FormBlock;
use Markwalet\NovaModalResponse\Blocks\Inlineable;
use Markwalet\NovaModalResponse\Tests\TestCase;
use stdClass;

class FormBlockTest extends TestCase
{
    public function test_factory_with_only_action_class_returns_a_form_block(): void
    {
        $block = Block::form(FakeFormAction::class);

        $this->assertInstanceOf(FormBlock::class, $block);
    }

    public function test_factory_with_label_and_action_class_returns_a_form_block(): void
    {
        $block = Block::form('Save', FakeFormAction::class);

        $this->assertInstanceOf(FormBlock::class, $block);
    }

    public function test_form_block_is_not_an_inline_atom(): void
    {
        $this->assertNotInstanceOf(Inlineable::class, Block::form(FakeFormAction::class));
    }

    public function test_label_defaults_to_the_actions_confirm_button_text(): void
    {
        $block = Block::form(FakeFormAction::class);

        $this->assertSame('Submit feedback', $block->toArray()['value']);
    }

    public function test_label_falls_back_to_action_name_when_confirm_button_text_is_default(): void
    {
        $block = Block::form(FakeDefaultConfirmAction::class);

        // Default confirmButtonText is 'Run Action', so that is what we get.
        $this->assertSame('Run Action', $block->toArray()['value']);
    }

    public function test_explicit_label_overrides_the_actions_confirm_button_text(): void
    {
        $block = Block::form('Send it', FakeFormAction::class);

        $this->assertSame('Send it', $block->toArray()['value']);
    }

    public function test_serialization_carries_type_uri_key_and_fields(): void
    {
        $payload = Block::form(FakeFormAction::class)->toArray();

        $this->assertSame('form', $payload['type']);
        $this->assertSame('fake-form', $payload['action']);
        $this->assertIsArray($payload['fields']);
        $this->assertNotEmpty($payload['fields']);
    }

    public function test_serialized_fields_carry_attribute_and_component(): void
    {
        $payload = Block::form(FakeFormAction::class)->toArray();
        $field = $payload['fields'][0];

        $this->assertSame('comment', $field->attribute);
        $this->assertNotEmpty($field->component);
    }

    public function test_reload_defaults_to_true(): void
    {
        $payload = Block::form(FakeFormAction::class)->toArray();

        $this->assertTrue($payload['reload']);
    }

    public function test_without_reload_flips_the_reload_flag(): void
    {
        $payload = Block::form(FakeFormAction::class)->withoutReload()->toArray();

        $this->assertFalse($payload['reload']);
    }

    public function test_serialization_includes_origin_context_keys(): void
    {
        $payload = Block::form(FakeFormAction::class)->toArray();

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

        $payload = Block::form(FakeFormAction::class)->toArray();

        $this->assertSame('users', $payload['resourceName']);
        $this->assertSame(['1', '2'], $payload['resources']);
    }

    public function test_serialization_throws_when_target_action_has_no_fields(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Block::action');

        Block::form(FakeFieldlessForFormAction::class)->toArray();
    }

    public function test_factory_rejects_non_action_class(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Block::form(stdClass::class);
    }

    public function test_a_form_block_is_rejected_inside_an_inline_group(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Block::inline([
            Block::text('Status:'),
            Block::form(FakeFormAction::class),
        ]);
    }
}

class FakeFormAction extends Action
{
    public $name = 'Fake form';

    public $confirmButtonText = 'Submit feedback';

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

class FakeDefaultConfirmAction extends Action
{
    public $name = 'Fake default confirm';

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

class FakeFieldlessForFormAction extends Action
{
    public $name = 'Fake fieldless for form';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        return ActionResponse::message('ok');
    }
}
