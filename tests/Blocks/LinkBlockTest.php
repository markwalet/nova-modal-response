<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\Inlineable;
use Markwalet\NovaModalResponse\Blocks\LinkBlock;
use Markwalet\NovaModalResponse\Enums\Size;
use Markwalet\NovaModalResponse\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ValueError;

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
            'size' => 'medium',
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
            'size' => 'medium',
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
            'size' => 'medium',
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
            'size' => 'medium',
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
            'size' => 'medium',
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
            'size' => 'medium',
            'icon' => 'arrow-top-right-on-square',
            'iconPosition' => 'trailing',
        ], $block->toArray());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function sizeProvider(): array
    {
        return [
            'small' => ['small'],
            'medium' => ['medium'],
            'large' => ['large'],
        ];
    }

    #[DataProvider('sizeProvider')]
    public function test_each_size_method_sets_the_size(string $size): void
    {
        $this->assertSame($size, Block::link('x', 'https://example.test')->{$size}()->toArray()['size']);
    }

    public function test_size_accepts_a_string_and_enum(): void
    {
        $this->assertSame('large', Block::link('x', 'https://example.test')->size('large')->toArray()['size']);
        $this->assertSame('small', Block::link('x', 'https://example.test')->size(Size::SMALL)->toArray()['size']);
    }

    public function test_size_rejects_an_unknown_string(): void
    {
        $this->expectException(ValueError::class);

        Block::link('x', 'https://example.test')->size('huge');
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
                ['type' => 'link', 'value' => 'Docs', 'href' => 'https://example.test', 'appearance' => 'link', 'newTab' => true, 'size' => 'medium', 'icon' => null, 'iconPosition' => 'leading'],
            ],
        ], $block->toArray());
    }
}
