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
            static function (Block $block) use ($withoutHighlight): array {
                $serialized = $block->toArray();

                if ($withoutHighlight && in_array($serialized['type'] ?? null, ['code', 'json'], true)) {
                    $serialized['highlight'] = false;
                }

                return $serialized;
            },
            array_values($blocks),
        );

        return $payload;
    }
}
