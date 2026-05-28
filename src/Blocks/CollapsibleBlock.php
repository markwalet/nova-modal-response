<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Markwalet\NovaModalResponse\Block;
use Stringable;

class CollapsibleBlock implements Renderable
{
    private bool $expanded = false;

    /** @var list<Renderable> */
    private readonly array $blocks;

    /**
     * @param array<int, Renderable|string|Stringable> $blocks
     */
    public function __construct(private readonly string $header, array $blocks)
    {
        $this->blocks = array_map(
            static fn (mixed $block): Renderable => Block::normalize($block),
            array_values($blocks),
        );
    }

    public function expanded(bool|callable $result = true): static
    {
        $this->expanded = (bool) value($result);

        return $this;
    }

    public function collapsed(bool|callable $result = true): static
    {
        $this->expanded = ! value($result);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'collapsible',
            'header' => $this->header,
            'expanded' => $this->expanded,
            'value' => array_map(static fn (Renderable $block): array => $block->toArray(), $this->blocks),
        ];
    }
}
