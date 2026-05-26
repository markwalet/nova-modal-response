<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

abstract class Block
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

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

    public static function heading(string|Stringable $value): HeadingBlock
    {
        return new HeadingBlock($value);
    }

    /**
     * @param array<int, string|\Stringable> $items
     */
    public static function list(array $items): ListBlock
    {
        return new ListBlock($items);
    }

    public static function badge(string|Stringable $value): BadgeBlock
    {
        return new BadgeBlock($value);
    }
}
