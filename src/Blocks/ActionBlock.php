<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;
use InvalidArgumentException;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Http\Requests\NovaRequest;
use LogicException;

/**
 * A fieldless dispatcher: a button that, when clicked, POSTs to Nova's action
 * endpoint against the origin context (the parent modal's resource +
 * selection). The Vue side intercepts the click rather than handing off to
 * Nova's runner so the package can own the close-then-open sequencing — Nova
 * stacks modals, we don't.
 *
 * Inlineable / Atom: fieldless dispatch is safe on a single horizontal row,
 * because an action block can never carry fields by construction (the
 * fields-guard in toArray() throws if the target action declares any).
 */
class ActionBlock implements Inlineable, Renderable
{
    /**
     * @var class-string<Action>
     */
    private readonly string $actionClass;

    /**
     * Whether a successful dispatch should reload the underlying resource
     * view. Defaults to true to mirror Nova's native action behavior; flip
     * to false via {@see withoutReload()} for read-only/preview actions.
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
                "Action block expects a Nova Action class; [{$actionClass}] is not a subclass of ".Action::class.'.'
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

        $this->guardAgainstFieldfulAction($action);

        return [
            'type' => 'action',
            'value' => (string) ($this->label ?? $action->name()),
            'action' => $action->uriKey(),
            'reload' => $this->reload,
            ...$this->originContext(),
        ];
    }

    private function guardAgainstFieldfulAction(Action $action): void
    {
        $request = Container::getInstance()->bound(NovaRequest::class)
            ? Container::getInstance()->make(NovaRequest::class)
            : NovaRequest::createFromBase(app('request'));

        $fields = $action->fields($request);

        if (! empty($fields)) {
            throw new LogicException(
                'Action block target ['.$this->actionClass.'] declares fields(); '
                .'use Block::form() to submit a fieldful action instead.'
            );
        }
    }

    /**
     * Capture the parent modal's resource + selected ids from the active
     * Nova action request, so the Vue side can rebuild the endpoint when the
     * button is clicked. Outside an HTTP request (e.g. in serialization tests
     * with no request bound), both fall back to empty.
     *
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
