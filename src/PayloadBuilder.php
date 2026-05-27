<?php

namespace Markwalet\NovaModalResponse;

use Markwalet\NovaModalResponse\Blocks\Renderable;

class PayloadBuilder
{
    /**
     * @param array<int, Renderable> $blocks
     * @param array<string, mixed> $chrome
     * @return array<string, mixed>
     */
    public function build(array $blocks, array $chrome): array
    {
        $payload = $chrome;

        $payload['blocks'] = array_map(
            static fn (Renderable $block): array => $block->toArray(),
            array_values($blocks),
        );

        return $payload;
    }
}
