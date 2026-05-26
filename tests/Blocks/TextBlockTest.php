<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\TextBlock;
use Markwalet\NovaModalResponse\Tests\TestCase;

class TextBlockTest extends TestCase
{
    public function test_it_serialises_to_a_typed_text_block(): void
    {
        $block = new TextBlock('hi');

        $this->assertSame(
            ['type' => 'text', 'value' => 'hi'],
            $block->toArray(),
        );
    }
}
