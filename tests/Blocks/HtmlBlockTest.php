<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\HtmlBlock;
use Markwalet\NovaModalResponse\ModalResponse;
use Markwalet\NovaModalResponse\Tests\TestCase;

class HtmlBlockTest extends TestCase
{
    public function test_it_serialises_to_a_typed_html_block(): void
    {
        $block = new HtmlBlock('<b>hi</b>');

        $this->assertSame(
            ['type' => 'html', 'value' => '<b>hi</b>'],
            $block->toArray(),
        );
    }

    public function test_factory_returns_an_html_block(): void
    {
        $this->assertInstanceOf(HtmlBlock::class, Block::html('<b>x</b>'));
    }

    public function test_html_sugar_produces_a_single_html_block_stack(): void
    {
        $response = ModalResponse::html('<b>hi</b>');

        $this->assertSame([
            'blocks' => [
                ['type' => 'html', 'value' => '<b>hi</b>'],
            ],
        ], $response['modal']->payload);
    }
}
