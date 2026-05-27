<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\Inlineable;
use Markwalet\NovaModalResponse\Blocks\LinkBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class LinkBlockTest extends TestCase
{
    public function test_factory_returns_a_same_tab_link_appearance_by_default(): void
    {
        $block = Block::link('Docs', 'https://example.test');

        $this->assertInstanceOf(LinkBlock::class, $block);
        $this->assertSame([
            'type' => 'link',
            'value' => 'Docs',
            'href' => 'https://example.test',
            'appearance' => 'link',
            'newTab' => false,
            'icon' => null,
            'iconPosition' => 'leading',
        ], $block->toArray());
    }

    public function test_new_tab_flips_the_new_tab_flag(): void
    {
        $block = Block::link('Docs', 'https://example.test')->newTab();

        $this->assertSame([
            'type' => 'link',
            'value' => 'Docs',
            'href' => 'https://example.test',
            'appearance' => 'link',
            'newTab' => true,
            'icon' => null,
            'iconPosition' => 'leading',
        ], $block->toArray());
    }

    public function test_button_switches_the_appearance(): void
    {
        $block = Block::link('Open', 'https://example.test')->button();

        $this->assertSame([
            'type' => 'link',
            'value' => 'Open',
            'href' => 'https://example.test',
            'appearance' => 'button',
            'newTab' => false,
            'icon' => null,
            'iconPosition' => 'leading',
        ], $block->toArray());
    }

    public function test_appearance_and_new_tab_compose(): void
    {
        $block = Block::link('Open', 'https://example.test')->button()->newTab();

        $this->assertSame([
            'type' => 'link',
            'value' => 'Open',
            'href' => 'https://example.test',
            'appearance' => 'button',
            'newTab' => true,
            'icon' => null,
            'iconPosition' => 'leading',
        ], $block->toArray());
    }

    public function test_icon_embeds_a_leading_icon_by_default(): void
    {
        $block = Block::link('Add', 'https://example.test')->button()->icon('plus');

        $this->assertSame([
            'type' => 'link',
            'value' => 'Add',
            'href' => 'https://example.test',
            'appearance' => 'button',
            'newTab' => false,
            'icon' => 'plus',
            'iconPosition' => 'leading',
        ], $block->toArray());
    }

    public function test_icon_can_be_placed_trailing(): void
    {
        $block = Block::link('Docs', 'https://example.test')->icon('arrow-top-right-on-square', trailing: true);

        $this->assertSame([
            'type' => 'link',
            'value' => 'Docs',
            'href' => 'https://example.test',
            'appearance' => 'link',
            'newTab' => false,
            'icon' => 'arrow-top-right-on-square',
            'iconPosition' => 'trailing',
        ], $block->toArray());
    }

    public function test_a_link_is_an_inline_atom(): void
    {
        $this->assertInstanceOf(Inlineable::class, Block::link('Docs', 'https://example.test'));
    }

    public function test_a_link_is_accepted_inside_an_inline_group(): void
    {
        $block = Block::inline([
            Block::text('See'),
            Block::link('Docs', 'https://example.test')->newTab(),
        ]);

        $this->assertSame([
            'type' => 'inline',
            'alignment' => 'default',
            'value' => [
                ['type' => 'text', 'value' => 'See'],
                ['type' => 'link', 'value' => 'Docs', 'href' => 'https://example.test', 'appearance' => 'link', 'newTab' => true, 'icon' => null, 'iconPosition' => 'leading'],
            ],
        ], $block->toArray());
    }
}
