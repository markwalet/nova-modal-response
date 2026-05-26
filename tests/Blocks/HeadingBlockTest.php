<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\HeadingBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class HeadingBlockTest extends TestCase
{
    public function test_factory_returns_a_heading_block_with_default_medium_size(): void
    {
        $block = Block::heading('Result');

        $this->assertInstanceOf(HeadingBlock::class, $block);
        $this->assertSame(
            ['type' => 'heading', 'value' => 'Result', 'size' => 'medium'],
            $block->toArray(),
        );
    }

    public function test_small_sets_size_to_small(): void
    {
        $this->assertSame(
            ['type' => 'heading', 'value' => 'x', 'size' => 'small'],
            Block::heading('x')->small()->toArray(),
        );
    }

    public function test_medium_sets_size_to_medium(): void
    {
        $this->assertSame(
            ['type' => 'heading', 'value' => 'x', 'size' => 'medium'],
            Block::heading('x')->large()->medium()->toArray(),
        );
    }

    public function test_large_sets_size_to_large(): void
    {
        $this->assertSame(
            ['type' => 'heading', 'value' => 'x', 'size' => 'large'],
            Block::heading('x')->large()->toArray(),
        );
    }
}
