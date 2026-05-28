<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

class HtmlBlock implements Renderable
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
