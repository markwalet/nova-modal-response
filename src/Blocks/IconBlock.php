<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;
use Markwalet\NovaModalResponse\Blocks\Concerns\HasSize;
use Markwalet\NovaModalResponse\Blocks\Concerns\HasVariants;

class IconBlock implements Inlineable, Renderable
{
    use HasSize;
    use HasVariants;

    public function __construct(private readonly string|Stringable $name) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'icon',
            'value' => (string) $this->name,
            'variant' => $this->variant->value,
            'size' => $this->size->value,
        ];
    }
}
