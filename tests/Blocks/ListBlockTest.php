<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Illuminate\Support\Stringable;
use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\ListBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class ListBlockTest extends TestCase
{
    public function test_factory_returns_an_unordered_list_block_by_default(): void
    {
        $block = Block::list(['a', 'b']);

        $this->assertInstanceOf(ListBlock::class, $block);
        $this->assertSame(
            ['type' => 'list', 'value' => ['a', 'b'], 'ordered' => false],
            $block->toArray(),
        );
    }

    public function test_ordered_flips_the_style(): void
    {
        $this->assertSame(
            ['type' => 'list', 'value' => ['a', 'b'], 'ordered' => true],
            Block::list(['a', 'b'])->ordered()->toArray(),
        );
    }

    public function test_stringable_items_are_coerced_to_strings(): void
    {
        $this->assertSame(
            ['type' => 'list', 'value' => ['a', 'b'], 'ordered' => false],
            Block::list(['a', new Stringable('b')])->toArray(),
        );
    }
}
