<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

class HtmlBlock extends Block
{
    public function __construct(private readonly string|Stringable $value) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'html',
            'value' => (string) $this->value,
        ];
    }
}
