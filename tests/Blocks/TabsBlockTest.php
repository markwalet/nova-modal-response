<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use InvalidArgumentException;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\Tab;
use Markwalet\NovaModalResponse\Blocks\TabsBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class TabsBlockTest extends TestCase
{
    public function test_factory_returns_a_tabs_block_with_inactive_tabs_by_default(): void
    {
        $block = Block::tabs([
            Block::tab('Overview', [Block::text('Hello')]),
        ]);

        $this->assertInstanceOf(TabsBlock::class, $block);
        $this->assertSame([
            'type' => 'tabs',
            'value' => [
                [
                    'label' => 'Overview',
                    'active' => false,
                    'value' => [
                        ['type' => 'text', 'value' => 'Hello'],
                    ],
                ],
            ],
        ], $block->toArray());
    }

    public function test_an_empty_tabs_block_renders_an_empty_value_list(): void
    {
        $this->assertSame([
            'type' => 'tabs',
            'value' => [],
        ], Block::tabs([])->toArray());
    }

    public function test_an_empty_tab_panel_is_allowed(): void
    {
        $block = Block::tabs([Block::tab('Empty', [])]);

        $this->assertSame([
            'type' => 'tabs',
            'value' => [
                ['label' => 'Empty', 'active' => false, 'value' => []],
            ],
        ], $block->toArray());
    }

    public function test_active_marks_the_tab_as_initially_visible(): void
    {
        $tab = Block::tab('Logs', [])->active();

        $this->assertTrue($tab->toArray()['active']);
    }

    public function test_active_resolves_a_callable_argument(): void
    {
        $on = Block::tab('Logs', [])->active(fn (): bool => true);
        $off = Block::tab('Logs', [])->active(fn (): bool => false);

        $this->assertTrue($on->toArray()['active']);
        $this->assertFalse($off->toArray()['active']);
    }

    public function test_active_false_deactivates_the_tab(): void
    {
        $tab = Block::tab('Logs', [])->active(false);

        $this->assertFalse($tab->toArray()['active']);
    }

    public function test_last_active_call_wins(): void
    {
        $tab = Block::tab('Logs', [])->active()->active(false);

        $this->assertFalse($tab->toArray()['active']);
    }

    public function test_string_keyed_array_entry_becomes_a_tab(): void
    {
        $block = Block::tabs([
            'Overview' => [Block::text('Hi')],
        ]);

        $this->assertSame([
            'type' => 'tabs',
            'value' => [
                [
                    'label' => 'Overview',
                    'active' => false,
                    'value' => [['type' => 'text', 'value' => 'Hi']],
                ],
            ],
        ], $block->toArray());
    }

    public function test_mixed_array_normalizes_tab_instances_and_string_keyed_entries(): void
    {
        $block = Block::tabs([
            Block::tab('Logs', [Block::text('log line')])->active(),
            'Overview' => [Block::text('overview text')],
        ]);

        $this->assertSame([
            'type' => 'tabs',
            'value' => [
                [
                    'label' => 'Logs',
                    'active' => true,
                    'value' => [['type' => 'text', 'value' => 'log line']],
                ],
                [
                    'label' => 'Overview',
                    'active' => false,
                    'value' => [['type' => 'text', 'value' => 'overview text']],
                ],
            ],
        ], $block->toArray());
    }

    public function test_bare_strings_inside_a_tab_are_coerced_to_text_blocks(): void
    {
        $block = Block::tabs([Block::tab('Notes', ['Hello'])]);

        $this->assertSame([
            'type' => 'tabs',
            'value' => [
                [
                    'label' => 'Notes',
                    'active' => false,
                    'value' => [['type' => 'text', 'value' => 'Hello']],
                ],
            ],
        ], $block->toArray());
    }

    public function test_tabs_can_be_nested(): void
    {
        $block = Block::tabs([
            Block::tab('Outer', [
                Block::tabs([
                    Block::tab('Inner', [Block::text('Deep')]),
                ]),
            ]),
        ]);

        $this->assertSame([
            'type' => 'tabs',
            'value' => [
                [
                    'label' => 'Outer',
                    'active' => false,
                    'value' => [
                        [
                            'type' => 'tabs',
                            'value' => [
                                [
                                    'label' => 'Inner',
                                    'active' => false,
                                    'value' => [['type' => 'text', 'value' => 'Deep']],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $block->toArray());
    }

    public function test_a_bare_array_without_a_string_key_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('a tab needs a label');

        Block::tabs([[Block::text('orphan')]]);
    }

    public function test_a_non_tab_object_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('a tab needs a label');

        Block::tabs([Block::text('not a tab')]);
    }

    public function test_a_scalar_entry_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('a tab needs a label');

        Block::tabs(['Overview']);
    }

    public function test_a_tab_value_object_serializes_without_a_type_key(): void
    {
        $tab = new Tab('Logs', [Block::text('hi')]);

        $this->assertArrayNotHasKey('type', $tab->toArray());
        $this->assertSame(['label', 'active', 'value'], array_keys($tab->toArray()));
    }
}
