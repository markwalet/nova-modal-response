<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;
use Markwalet\NovaModalResponse\Blocks\Concerns\HasSize;

class HeadingBlock implements Renderable
{
    use HasSize;

    public function __construct(private readonly string|Stringable $value) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'heading',
            'value' => (string) $this->value,
            'size' => $this->size->value,
        ];
    }
}
