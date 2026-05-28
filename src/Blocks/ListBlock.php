<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Stringable;

class ListBlock implements Renderable
{
    private bool $ordered = false;

    /**
     * @param array<int, string|Stringable> $items
     */
    public function __construct(private readonly array $items) {}

    public function ordered(): self
    {
        $this->ordered = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'list',
            'value' => array_map(static fn (string|Stringable $item): string => (string) $item, $this->items),
            'ordered' => $this->ordered,
        ];
    }
}
