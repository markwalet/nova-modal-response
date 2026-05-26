<?php

namespace Markwalet\NovaModalResponse;

use Markwalet\NovaModalResponse\Blocks\Block;

class PayloadBuilder
{
    /**
     * @param array<int, Block> $blocks
     * @param array<string, mixed> $chrome
     * @return array<string, mixed>
     */
    public function build(array $blocks, array $chrome, bool $withoutHighlight): array
    {
        $payload = $chrome;

        $payload['blocks'] = array_map(
            static fn (Block $block): array => $block->toArray(),
            array_values($blocks),
        );

        return $payload;
    }
}
