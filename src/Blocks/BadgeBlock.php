<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;
use Markwalet\NovaModalResponse\Blocks\Concerns\HasVariants;

class BadgeBlock implements Inlineable, Renderable
{
    use HasVariants;

    public function __construct(private readonly string|Stringable $value) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'badge',
            'value' => (string) $this->value,
            'variant' => $this->variant,
        ];
    }
}
