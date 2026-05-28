<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;
use InvalidArgumentException;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use LogicException;

/**
 * Renders a Nova action's fields() inline inside the modal body, then submits
 * the collected values through the shared dispatch core (same intercept,
 * origin context, close-then-open behaviour as the action block — #70). The
 * form block is the only construct that carries user-facing fields.
 *
 * Block-level, not an atom: keeps fieldful forms out of cramped inline rows.
 */
class FormBlock implements Renderable
{
    /**
     * @var class-string<Action>
     */
    private readonly string $actionClass;

    /**
     * Whether a successful dispatch should reload the underlying resource
     * view. Mirrors the action block knob — flip to false via
     * {@see withoutReload()} for read-only/preview actions.
     */
    private bool $reload = true;

    /**
     * @param class-string<Action> $actionClass
     */
    public function __construct(
        private readonly string|Stringable|null $label,
        string $actionClass,
    ) {
        if (! is_subclass_of($actionClass, Action::class)) {
            throw new InvalidArgumentException(
                "Form block expects a Nova Action class; [{$actionClass}] is not a subclass of ".Action::class.'.'
            );
        }

        $this->actionClass = $actionClass;
    }

    /**
     * Suppress the post-dispatch resource view reload. Use for actions that
     * don't mutate the underlying data (read-only / preview).
     */
    public function withoutReload(): self
    {
        $this->reload = false;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $action = new $this->actionClass;
        $request = $this->novaRequest();
        $fields = $this->resolveFields($action, $request);

        return [
            'type' => 'form',
            'value' => (string) ($this->label ?? $action->confirmButtonText),
            'action' => $action->uriKey(),
            'fields' => $fields,
            'reload' => $this->reload,
            ...$this->originContext(),
        ];
    }

    /**
     * Run the same field pipeline Nova's action endpoint uses (see
     * Action::jsonSerialize), so the Vue side can hand the result straight to
     * the registered `form-*` field components.
     *
     * @return array<int, mixed>
     */
    private function resolveFields(Action $action, NovaRequest $request): array
    {
        $fields = FieldCollection::make($action->fields($request))
            ->filter->authorizedToSee($request)
            ->each->resolveForAction($request)
            ->applyDependsOnWithDefaultValues($request)
            ->values()
            ->all();

        if (empty($fields)) {
            throw new LogicException(
                'Form block target ['.$this->actionClass.'] does not declare fields(); '
                .'use Block::action() for a fieldless dispatch instead.'
            );
        }

        return $fields;
    }

    private function novaRequest(): NovaRequest
    {
        return Container::getInstance()->bound(NovaRequest::class)
            ? Container::getInstance()->make(NovaRequest::class)
            : NovaRequest::createFromBase(app('request'));
    }

    /**
     * @return array{resourceName: string|null, resources: array<int, string>}
     */
    private function originContext(): array
    {
        /** @var Request|null $request */
        $request = Container::getInstance()->bound('request')
            ? Container::getInstance()->make('request')
            : null;

        $resourceName = null;
        $resources = [];

        if ($request !== null) {
            $route = $request->route('resource');
            if (is_string($route) && $route !== '') {
                $resourceName = $route;
            }

            $input = $request->input('resources');
            if (is_array($input)) {
                $resources = array_values(array_map('strval', $input));
            } elseif (is_string($input) && $input !== '') {
                $resources = [$input];
            }
        }

        return [
            'resourceName' => $resourceName,
            'resources' => $resources,
        ];
    }
}
