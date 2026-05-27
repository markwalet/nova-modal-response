<?php

namespace Markwalet\NovaModalResponse\Tests;

use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class HighlightingTest extends TestCase
{
    public function test_code_sugar_highlights_by_default(): void
    {
        $response = ModalResponse::code('echo 1;');

        $this->assertSame([
            'blocks' => [
                ['type' => 'code', 'value' => 'echo 1;', 'highlight' => true],
            ],
        ], $response['modal']->payload);
    }

    public function test_code_sugar_flag_disables_highlighting(): void
    {
        $response = ModalResponse::code('echo 1;', highlight: false);

        $this->assertSame([
            'blocks' => [
                ['type' => 'code', 'value' => 'echo 1;', 'highlight' => false],
            ],
        ], $response['modal']->payload);
    }

    public function test_json_sugar_highlights_by_default(): void
    {
        $response = ModalResponse::json(['ok' => true]);

        $this->assertSame([
            'blocks' => [
                ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => true],
            ],
        ], $response['modal']->payload);
    }

    public function test_json_sugar_flag_disables_highlighting(): void
    {
        $response = ModalResponse::json(['ok' => true], highlight: false);

        $this->assertSame([
            'blocks' => [
                ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => false],
            ],
        ], $response['modal']->payload);
    }

    public function test_highlighting_is_controlled_per_block_inside_a_stack(): void
    {
        $response = ModalResponse::stack([
            Block::text('intro'),
            Block::code('echo 1;')->withoutHighlighting(),
            Block::json(['ok' => true]),
        ]);

        $this->assertSame([
            ['type' => 'text', 'value' => 'intro'],
            ['type' => 'code', 'value' => 'echo 1;', 'highlight' => false],
            ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => true],
        ], $response['modal']->payload['blocks']);
    }

    public function test_wire_carries_no_top_level_highlight_field(): void
    {
        $response = ModalResponse::code('echo 1;', highlight: false);

        $this->assertArrayNotHasKey('highlight', $response['modal']->payload);
    }
}
