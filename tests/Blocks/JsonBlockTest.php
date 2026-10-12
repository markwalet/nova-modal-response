<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use JsonException;
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\Blocks\JsonBlock;
use Markwalet\NovaModalResponse\ModalResponse;
use Markwalet\NovaModalResponse\Tests\TestCase;

class JsonBlockTest extends TestCase
{
    public function test_factory_pretty_prints_and_defaults_to_highlight(): void
    {
        $block = Block::json(['ok' => true]);

        $this->assertInstanceOf(JsonBlock::class, $block);
        $this->assertSame(
            [
                'type' => 'json',
                'value' => "{\n    \"ok\": true\n}",
                'highlight' => true,
            ],
            $block->toArray(),
        );
    }

    public function test_without_highlighting_disables_highlight(): void
    {
        $this->assertSame(
            [
                'type' => 'json',
                'value' => "{\n    \"ok\": true\n}",
                'highlight' => false,
            ],
            Block::json(['ok' => true])->withoutHighlighting()->toArray(),
        );
    }

    public function test_invalid_utf8_throws_a_json_exception(): void
    {
        $this->expectException(JsonException::class);

        Block::json(['x' => "\xB1\x31"]);
    }

    public function test_json_sugar_produces_a_single_json_block_stack(): void
    {
        $response = ModalResponse::json(['ok' => true]);

        $this->assertSame([
            'blocks' => [
                [
                    'type' => 'json',
                    'value' => "{\n    \"ok\": true\n}",
                    'highlight' => true,
                ],
            ],
        ], $response['modal']->payload);
    }
}
