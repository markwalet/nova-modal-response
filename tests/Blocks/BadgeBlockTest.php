<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\BadgeBlock;
use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class BadgeBlockTest extends TestCase
{
    public function test_factory_returns_a_badge_with_default_variant(): void
    {
        $block = Block::badge('New');

        $this->assertInstanceOf(BadgeBlock::class, $block);
        $this->assertSame(
            ['type' => 'badge', 'value' => 'New', 'variant' => 'default'],
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
            ['type' => 'badge', 'value' => 'x', 'variant' => $variant],
            Block::badge('x')->{$variant}()->toArray(),
        );
    }
}
