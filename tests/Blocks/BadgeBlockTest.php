<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\BadgeBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class BadgeBlockTest extends TestCase
{
    public function test_factory_returns_a_badge_with_default_variant(): void
    {
        $block = Block::badge('New');

        $this->assertInstanceOf(BadgeBlock::class, $block);
        $this->assertSame(
            ['type' => 'badge', 'value' => 'New', 'variant' => 'default', 'icon' => null, 'iconPosition' => 'leading'],
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
            ['type' => 'badge', 'value' => 'x', 'variant' => $variant, 'icon' => null, 'iconPosition' => 'leading'],
            Block::badge('x')->{$variant}()->toArray(),
        );
    }

    public function test_icon_embeds_a_leading_icon_by_default(): void
    {
        $this->assertSame(
            ['type' => 'badge', 'value' => 'Live', 'variant' => 'success', 'icon' => 'check-circle', 'iconPosition' => 'leading'],
            Block::badge('Live')->success()->icon('check-circle')->toArray(),
        );
    }

    public function test_icon_can_be_placed_trailing(): void
    {
        $this->assertSame(
            ['type' => 'badge', 'value' => 'Live', 'variant' => 'default', 'icon' => 'check-circle', 'iconPosition' => 'trailing'],
            Block::badge('Live')->icon('check-circle', trailing: true)->toArray(),
        );
    }
}
