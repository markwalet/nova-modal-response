<?php

namespace Markwalet\NovaModalResponse\Blocks;

class DividerBlock implements Renderable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => 'divider'];
    }
}
