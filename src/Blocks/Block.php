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
}
