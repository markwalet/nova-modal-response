<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\BadgeBlock;
use Markwalet\NovaModalResponse\Enums\Size;
use Markwalet\NovaModalResponse\Enums\Variant;
use Markwalet\NovaModalResponse\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ValueError;

class BadgeBlockTest extends TestCase
{
    public function test_factory_returns_a_badge_with_default_variant(): void
    {
        $block = Block::badge('New');

        $this->assertInstanceOf(BadgeBlock::class, $block);
        $this->assertSame(
            ['type' => 'badge', 'value' => 'New', 'variant' => 'default', 'size' => 'medium', 'icon' => null, 'iconPosition' => 'leading'],
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
            ['type' => 'badge', 'value' => 'x', 'variant' => $variant, 'size' => 'medium', 'icon' => null, 'iconPosition' => 'leading'],
            Block::badge('x')->{$variant}()->toArray(),
        );
    }

    public function test_icon_embeds_a_leading_icon_by_default(): void
    {
        $this->assertSame(
            ['type' => 'badge', 'value' => 'Live', 'variant' => 'success', 'size' => 'medium', 'icon' => 'check-circle', 'iconPosition' => 'leading'],
            Block::badge('Live')->success()->icon('check-circle')->toArray(),
        );
    }

    public function test_icon_can_be_placed_trailing(): void
    {
        $this->assertSame(
            ['type' => 'badge', 'value' => 'Live', 'variant' => 'default', 'size' => 'medium', 'icon' => 'check-circle', 'iconPosition' => 'trailing'],
            Block::badge('Live')->icon('check-circle', trailing: true)->toArray(),
        );
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
        $this->assertSame(
            ['type' => 'badge', 'value' => 'x', 'variant' => 'default', 'size' => $size, 'icon' => null, 'iconPosition' => 'leading'],
            Block::badge('x')->{$size}()->toArray(),
        );
    }

    public function test_size_accepts_a_string(): void
    {
        $this->assertSame('large', Block::badge('x')->size('large')->toArray()['size']);
    }

    public function test_size_accepts_an_enum(): void
    {
        $this->assertSame('large', Block::badge('x')->size(Size::LARGE)->toArray()['size']);
    }

    public function test_size_rejects_an_unknown_string(): void
    {
        $this->expectException(ValueError::class);

        Block::badge('x')->size('huge');
    }

    public function test_variant_accepts_a_string(): void
    {
        $this->assertSame('warning', Block::badge('x')->variant('warning')->toArray()['variant']);
    }

    public function test_variant_accepts_an_enum(): void
    {
        $this->assertSame('warning', Block::badge('x')->variant(Variant::WARNING)->toArray()['variant']);
    }

    public function test_variant_rejects_an_unknown_string(): void
    {
        $this->expectException(ValueError::class);

        Block::badge('x')->variant('chartreuse');
    }
}
