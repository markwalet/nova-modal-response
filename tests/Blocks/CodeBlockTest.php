<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\CodeBlock;
use Markwalet\NovaModalResponse\ModalResponse;
use Markwalet\NovaModalResponse\Tests\TestCase;

class CodeBlockTest extends TestCase
{
    public function test_factory_returns_a_code_block_with_highlight_autodetect(): void
    {
        $block = Block::code('echo 1;');

        $this->assertInstanceOf(CodeBlock::class, $block);
        $this->assertSame(
            ['type' => 'code', 'value' => 'echo 1;', 'highlight' => true],
            $block->toArray(),
        );
    }

    public function test_language_sets_the_language_hint(): void
    {
        $this->assertSame(
            ['type' => 'code', 'value' => 'x', 'highlight' => true, 'language' => 'php'],
            Block::code('x')->language('php')->toArray(),
        );
    }

    public function test_without_highlighting_disables_highlighting(): void
    {
        $this->assertSame(
            ['type' => 'code', 'value' => 'x', 'highlight' => false],
            Block::code('x')->withoutHighlighting()->toArray(),
        );
    }

    public function test_code_sugar_produces_a_single_code_block_stack(): void
    {
        $response = ModalResponse::code('echo 1;');

        $this->assertSame([
            'blocks' => [
                ['type' => 'code', 'value' => 'echo 1;', 'highlight' => true],
            ],
        ], $response['modal']->payload);
    }
}
