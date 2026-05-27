<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use InvalidArgumentException;
use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\InlineBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class InlineBlockTest extends TestCase
{
    public function test_factory_returns_an_inline_block_packed_by_default(): void
    {
        $block = Block::inline([Block::text('Status'), Block::badge('Active')->success()]);

        $this->assertInstanceOf(InlineBlock::class, $block);
        $this->assertSame([
            'type' => 'inline',
            'spread' => false,
            'value' => [
                ['type' => 'text', 'value' => 'Status'],
                ['type' => 'badge', 'value' => 'Active', 'variant' => 'success'],
            ],
        ], $block->toArray());
    }

    public function test_atoms_keep_their_order(): void
    {
        $block = Block::inline([Block::badge('first'), Block::text('second')]);

        $this->assertSame(
            ['first', 'second'],
            array_column($block->toArray()['value'], 'value'),
        );
    }

    public function test_spread_flips_the_layout_knob(): void
    {
        $block = Block::inline([Block::text('key'), Block::badge('value')])->spread();

        $this->assertSame([
            'type' => 'inline',
            'spread' => true,
            'value' => [
                ['type' => 'text', 'value' => 'key'],
                ['type' => 'badge', 'value' => 'value', 'variant' => 'default'],
            ],
        ], $block->toArray());
    }

    public function test_atoms_serialize_through_their_own_to_array(): void
    {
        $block = Block::inline([Block::text('Plain')]);

        $this->assertSame(
            [['type' => 'text', 'value' => 'Plain']],
            $block->toArray()['value'],
        );
    }

    public function test_an_empty_group_serializes_to_an_empty_value_list(): void
    {
        $this->assertSame([
            'type' => 'inline',
            'spread' => false,
            'value' => [],
        ], Block::inline([])->toArray());
    }

    public function test_bare_strings_are_coerced_to_text_atoms(): void
    {
        $block = Block::inline(['Hello', Block::badge('New')]);

        $this->assertSame([
            'type' => 'inline',
            'spread' => false,
            'value' => [
                ['type' => 'text', 'value' => 'Hello'],
                ['type' => 'badge', 'value' => 'New', 'variant' => 'default'],
            ],
        ], $block->toArray());
    }

    public function test_a_non_block_non_string_atom_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Blocks must be Block instances or strings.');

        Block::inline([42]);
    }

    public function test_a_non_inlineable_block_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Inline group may only contain Inlineable atoms.');

        Block::inline([Block::text('ok'), Block::code('not allowed')]);
    }

    public function test_a_divider_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Block::inline([Block::divider()]);
    }

    public function test_a_list_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Block::inline([Block::list(['a', 'b'])]);
    }

    public function test_inline_groups_cannot_nest(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Block::inline([Block::inline([Block::text('inner')])]);
    }
}
