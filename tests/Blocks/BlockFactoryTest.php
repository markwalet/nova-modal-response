<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Illuminate\Support\Stringable;
use InvalidArgumentException;
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

    public function test_normalize_passes_block_instances_through_unchanged(): void
    {
        $block = Block::badge('New');

        $this->assertSame($block, Block::normalize($block));
    }

    public function test_normalize_coerces_a_string_into_a_text_block(): void
    {
        $block = Block::normalize('Hello');

        $this->assertInstanceOf(TextBlock::class, $block);
        $this->assertSame(['type' => 'text', 'value' => 'Hello'], $block->toArray());
    }

    public function test_normalize_coerces_a_stringable_into_a_text_block(): void
    {
        $block = Block::normalize(new Stringable('Hello'));

        $this->assertInstanceOf(TextBlock::class, $block);
        $this->assertSame(['type' => 'text', 'value' => 'Hello'], $block->toArray());
    }

    public function test_normalize_serializes_identically_to_an_explicit_text_block(): void
    {
        $this->assertSame(
            Block::text('Hello')->toArray(),
            Block::normalize('Hello')->toArray(),
        );
    }

    public function test_normalize_rejects_a_non_block_non_string_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Blocks must be Block instances or strings.');

        Block::normalize(42);
    }
}
