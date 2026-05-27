<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\ViewBlock;
use Markwalet\NovaModalResponse\ModalResponse;
use Markwalet\NovaModalResponse\Tests\TestCase;

class ViewBlockTest extends TestCase
{
    public function test_it_renders_a_named_view_to_a_typed_html_block(): void
    {
        $block = new ViewBlock('greeting', ['name' => 'World']);

        $this->assertSame(
            ['type' => 'html', 'value' => '<p>Hello, World!</p>'],
            $block->toArray(),
        );
    }

    public function test_factory_returns_a_view_block(): void
    {
        $this->assertInstanceOf(ViewBlock::class, Block::view('greeting', ['name' => 'World']));
    }

    public function test_factory_passes_view_data_through(): void
    {
        $block = Block::view('greeting', ['name' => 'Mark']);

        $this->assertSame(
            ['type' => 'html', 'value' => '<p>Hello, Mark!</p>'],
            $block->toArray(),
        );
    }

    public function test_view_sugar_produces_a_single_html_block_stack(): void
    {
        $response = ModalResponse::view('greeting', ['name' => 'World']);

        $this->assertSame([
            'blocks' => [
                ['type' => 'html', 'value' => '<p>Hello, World!</p>'],
            ],
        ], $response['modal']->payload);
    }
}
