<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\TextBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class BlockFactoryTest extends TestCase
{
    public function test_text_factory_returns_a_text_block(): void
    {
        $block = Block::text('hello');

        $this->assertInstanceOf(TextBlock::class, $block);
        $this->assertSame(['type' => 'text', 'value' => 'hello'], $block->toArray());
    }
}
