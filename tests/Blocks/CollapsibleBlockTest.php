<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use InvalidArgumentException;
use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\CollapsibleBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class CollapsibleBlockTest extends TestCase
{
    public function test_factory_returns_a_collapsed_collapsible_block_by_default(): void
    {
        $block = Block::collapsible('Details', [Block::text('Some explanation')]);

        $this->assertInstanceOf(CollapsibleBlock::class, $block);
        $this->assertSame([
            'type' => 'collapsible',
            'header' => 'Details',
            'expanded' => false,
            'value' => [
                ['type' => 'text', 'value' => 'Some explanation'],
            ],
        ], $block->toArray());
    }

    public function test_expanded_sets_the_open_state(): void
    {
        $block = Block::collapsible('Details', [Block::text('hi')])->expanded();

        $this->assertTrue($block->toArray()['expanded']);
    }

    public function test_collapsed_sets_the_closed_state(): void
    {
        $block = Block::collapsible('Details', [Block::text('hi')])->collapsed();

        $this->assertFalse($block->toArray()['expanded']);
    }

    public function test_expanded_false_collapses_the_block(): void
    {
        $block = Block::collapsible('Details', [Block::text('hi')])->expanded(false);

        $this->assertFalse($block->toArray()['expanded']);
    }

    public function test_collapsed_false_expands_the_block(): void
    {
        $block = Block::collapsible('Details', [Block::text('hi')])->collapsed(false);

        $this->assertTrue($block->toArray()['expanded']);
    }

    public function test_expanded_resolves_a_callable_argument(): void
    {
        $expanded = Block::collapsible('Details', [])->expanded(fn (): bool => true);
        $collapsed = Block::collapsible('Details', [])->expanded(fn (): bool => false);

        $this->assertTrue($expanded->toArray()['expanded']);
        $this->assertFalse($collapsed->toArray()['expanded']);
    }

    public function test_collapsed_resolves_a_callable_argument(): void
    {
        $collapsed = Block::collapsible('Details', [])->collapsed(fn (): bool => true);
        $expanded = Block::collapsible('Details', [])->collapsed(fn (): bool => false);

        $this->assertFalse($collapsed->toArray()['expanded']);
        $this->assertTrue($expanded->toArray()['expanded']);
    }

    public function test_last_state_call_wins(): void
    {
        $expandedLast = Block::collapsible('Details', [])->collapsed()->expanded();
        $collapsedLast = Block::collapsible('Details', [])->expanded()->collapsed();

        $this->assertTrue($expandedLast->toArray()['expanded']);
        $this->assertFalse($collapsedLast->toArray()['expanded']);
    }

    public function test_children_serialize_through_their_own_to_array(): void
    {
        $block = Block::collapsible('Details', [
            Block::text('Some explanation'),
            Block::list(['One', 'Two']),
            Block::badge('New')->success(),
        ]);

        $this->assertSame([
            'type' => 'collapsible',
            'header' => 'Details',
            'expanded' => false,
            'value' => [
                ['type' => 'text', 'value' => 'Some explanation'],
                ['type' => 'list', 'value' => ['One', 'Two'], 'ordered' => false],
                ['type' => 'badge', 'value' => 'New', 'variant' => 'success'],
            ],
        ], $block->toArray());
    }

    public function test_bare_strings_are_coerced_to_text_blocks(): void
    {
        $block = Block::collapsible('Details', ['Hello', Block::badge('New')]);

        $this->assertSame([
            'type' => 'collapsible',
            'header' => 'Details',
            'expanded' => false,
            'value' => [
                ['type' => 'text', 'value' => 'Hello'],
                ['type' => 'badge', 'value' => 'New', 'variant' => 'default'],
            ],
        ], $block->toArray());
    }

    public function test_an_empty_content_list_serializes_to_an_empty_value_list(): void
    {
        $this->assertSame([
            'type' => 'collapsible',
            'header' => 'Details',
            'expanded' => false,
            'value' => [],
        ], Block::collapsible('Details', [])->toArray());
    }

    public function test_a_non_block_non_string_child_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Blocks must be Block instances or strings.');

        Block::collapsible('Details', [42]);
    }
}
