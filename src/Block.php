<?php

namespace Markwalet\NovaModalResponse;

use Illuminate\Support\Stringable;
use InvalidArgumentException;
use JsonException;
use Laravel\Nova\Actions\Action;
use Markwalet\NovaModalResponse\Blocks\ActionBlock;
use Markwalet\NovaModalResponse\Blocks\BadgeBlock;
use Markwalet\NovaModalResponse\Blocks\CodeBlock;
use Markwalet\NovaModalResponse\Blocks\CollapsibleBlock;
use Markwalet\NovaModalResponse\Blocks\DividerBlock;
use Markwalet\NovaModalResponse\Blocks\FormBlock;
use Markwalet\NovaModalResponse\Blocks\HeadingBlock;
use Markwalet\NovaModalResponse\Blocks\HtmlBlock;
use Markwalet\NovaModalResponse\Blocks\IconBlock;
use Markwalet\NovaModalResponse\Blocks\InlineBlock;
use Markwalet\NovaModalResponse\Blocks\JsonBlock;
use Markwalet\NovaModalResponse\Blocks\LinkBlock;
use Markwalet\NovaModalResponse\Blocks\ListBlock;
use Markwalet\NovaModalResponse\Blocks\MarkdownBlock;
use Markwalet\NovaModalResponse\Blocks\Renderable;
use Markwalet\NovaModalResponse\Blocks\TextBlock;
use Markwalet\NovaModalResponse\Blocks\ViewBlock;
use Stringable as StringableInterface;

/**
 * Factory hub for the built-in block types, alongside ModalResponse in the main
 * namespace. Pure static facade: it builds blocks but is not itself a block.
 */
final class Block
{
    private function __construct() {}

    /**
     * Coerce a value into a block: instances pass through, strings become text blocks.
     */
    public static function normalize(mixed $block): Renderable
    {
        if ($block instanceof Renderable) {
            return $block;
        }

        if (is_string($block) || $block instanceof StringableInterface) {
            return self::text((string) $block);
        }

        throw new InvalidArgumentException('Blocks must be Block instances or strings.');
    }

    public static function text(string|Stringable $value): TextBlock
    {
        return new TextBlock($value);
    }

    public static function divider(): DividerBlock
    {
        return new DividerBlock;
    }

    public static function html(string|Stringable $value): HtmlBlock
    {
        return new HtmlBlock($value);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function view(string $view, array $data = []): ViewBlock
    {
        return new ViewBlock($view, $data);
    }

    public static function markdown(string|Stringable $content): MarkdownBlock
    {
        return new MarkdownBlock($content);
    }

    public static function heading(string|Stringable $value): HeadingBlock
    {
        return new HeadingBlock($value);
    }

    /**
     * @param array<int, string|StringableInterface> $items
     */
    public static function list(array $items): ListBlock
    {
        return new ListBlock($items);
    }

    public static function badge(string|Stringable $value): BadgeBlock
    {
        return new BadgeBlock($value);
    }

    public static function icon(string|Stringable $name): IconBlock
    {
        return new IconBlock($name);
    }

    public static function code(string|Stringable $value): CodeBlock
    {
        return new CodeBlock($value);
    }

    public static function link(string|Stringable $label, string|Stringable $href): LinkBlock
    {
        return new LinkBlock($label, $href);
    }

    /**
     * Build a fieldless action-dispatch button. Called either with the
     * action class on its own (label defaults to the action's `name()`) or
     * with an explicit label first followed by the action class.
     *
     *   Block::action(MyAction::class)
     *   Block::action('Custom label', MyAction::class)
     *
     * @param class-string<Action>|string|Stringable $labelOrAction
     * @param class-string<Action>|null $action
     */
    public static function action(string|Stringable $labelOrAction, ?string $action = null): ActionBlock
    {
        if ($action === null) {
            return new ActionBlock(null, (string) $labelOrAction);
        }

        return new ActionBlock($labelOrAction, $action);
    }

    /**
     * Build a fieldful form-dispatch block. Renders the target action's
     * fields() inline; submit label defaults to the action's
     * confirmButtonText (Nova's own default is 'Run Action').
     *
     *   Block::form(MyAction::class)
     *   Block::form('Save and run', MyAction::class)
     *
     * @param class-string<Action>|string|Stringable $labelOrAction
     * @param class-string<Action>|null $action
     */
    public static function form(string|Stringable $labelOrAction, ?string $action = null): FormBlock
    {
        if ($action === null) {
            return new FormBlock(null, (string) $labelOrAction);
        }

        return new FormBlock($labelOrAction, $action);
    }

    /**
     * @param array<int, Renderable|string|StringableInterface> $atoms
     */
    public static function inline(array $atoms): InlineBlock
    {
        return new InlineBlock($atoms);
    }

    /**
     * @param array<int, Renderable|string|StringableInterface> $blocks
     */
    public static function collapsible(string $header, array $blocks): CollapsibleBlock
    {
        return new CollapsibleBlock($header, $blocks);
    }

    /**
     * @param array<mixed> $value
     *
     * @throws JsonException
     */
    public static function json(array $value): JsonBlock
    {
        return new JsonBlock($value);
    }
}
