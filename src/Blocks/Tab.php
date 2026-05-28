<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Markwalet\NovaModalResponse\Block;
use Stringable;

/**
 * A single entry in a tabs block: a cosmetic label paired with a nested stack
 * of child blocks. Internal value object of the tabs block — deliberately NOT
 * a Renderable, so it is never dispatched through the block-component map.
 */
class Tab
{
    private bool $active = false;

    /** @var list<Renderable> */
    private readonly array $blocks;

    /**
     * @param array<int, Renderable|string|Stringable> $blocks
     */
    public function __construct(private readonly string $label, array $blocks)
    {
        $this->blocks = array_map(
            static fn (mixed $block): Renderable => Block::normalize($block),
            array_values($blocks),
        );
    }

    public function active(bool|callable $result = true): static
    {
        $this->active = (bool) value($result);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'active' => $this->active,
            'value' => array_map(static fn (Renderable $block): array => $block->toArray(), $this->blocks),
        ];
    }
}
