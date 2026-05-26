<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\DividerBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class DividerBlockTest extends TestCase
{
    public function test_it_serialises_to_a_typed_divider_block(): void
    {
        $block = new DividerBlock;

        $this->assertSame(['type' => 'divider'], $block->toArray());
    }

    public function test_factory_returns_a_divider_block(): void
    {
        $this->assertInstanceOf(DividerBlock::class, Block::divider());
    }
}
