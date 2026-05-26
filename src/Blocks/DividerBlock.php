<?php

namespace Markwalet\NovaModalResponse\Blocks;

class DividerBlock extends Block
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => 'divider'];
    }
}
