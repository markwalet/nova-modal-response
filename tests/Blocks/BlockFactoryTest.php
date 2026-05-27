<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\InlineBlock;
use Markwalet\NovaModalResponse\Blocks\LinkBlock;
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

    public function test_inline_factory_returns_an_inline_block(): void
    {
        $block = Block::inline([Block::text('hello')]);

        $this->assertInstanceOf(InlineBlock::class, $block);
    }

    public function test_link_factory_returns_a_link_block(): void
    {
        $block = Block::link('Docs', 'https://example.test');

        $this->assertInstanceOf(LinkBlock::class, $block);
    }
}
