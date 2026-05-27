<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;
use InvalidArgumentException;
use JsonException;
use Stringable as StringableInterface;

abstract class Block
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

    /**
     * Coerce a value into a Block: instances pass through, strings become text blocks.
     */
    public static function normalize(mixed $block): Block
    {
        if ($block instanceof Block) {
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
     * @param array<int, Block|string|StringableInterface> $atoms
     */
    public static function inline(array $atoms): InlineBlock
    {
        return new InlineBlock($atoms);
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
