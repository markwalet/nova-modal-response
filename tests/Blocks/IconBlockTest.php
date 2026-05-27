<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\IconBlock;
use Markwalet\NovaModalResponse\Blocks\Inlineable;
use Markwalet\NovaModalResponse\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IconBlockTest extends TestCase
{
    public function test_factory_returns_an_icon_with_default_variant(): void
    {
        $block = Block::icon('check-circle');

        $this->assertInstanceOf(IconBlock::class, $block);
        $this->assertSame(
            ['type' => 'icon', 'value' => 'check-circle', 'variant' => 'default'],
            $block->toArray(),
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function variantProvider(): array
    {
        return [
            'default' => ['default'],
            'info' => ['info'],
            'success' => ['success'],
            'warning' => ['warning'],
            'danger' => ['danger'],
        ];
    }

    #[DataProvider('variantProvider')]
    public function test_each_variant_method_sets_the_variant(string $variant): void
    {
        $this->assertSame(
            ['type' => 'icon', 'value' => 'check-circle', 'variant' => $variant],
            Block::icon('check-circle')->{$variant}()->toArray(),
        );
    }

    public function test_an_icon_is_an_inline_atom(): void
    {
        $this->assertInstanceOf(Inlineable::class, Block::icon('check-circle'));
    }

    public function test_an_icon_is_accepted_inside_an_inline_group(): void
    {
        $block = Block::inline([
            Block::icon('check-circle')->success(),
            Block::text('Done'),
        ]);

        $this->assertSame([
            'type' => 'inline',
            'alignment' => 'default',
            'value' => [
                ['type' => 'icon', 'value' => 'check-circle', 'variant' => 'success'],
                ['type' => 'text', 'value' => 'Done'],
            ],
        ], $block->toArray());
    }
}
