<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

class TextBlock implements Inlineable, Renderable
{
    public function __construct(private readonly string|Stringable $value) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'text',
            'value' => (string) $this->value,
        ];
    }
}
