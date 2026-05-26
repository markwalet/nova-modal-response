<?php

namespace Markwalet\NovaModalResponse\Tests;

use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class WithoutSyntaxHighlightingTest extends TestCase
{
    public function test_called_after_stack_disables_highlight_on_every_code_and_json_block(): void
    {
        $response = ModalResponse::stack([
            Block::code('echo 1;'),
            Block::json(['ok' => true]),
        ])->withoutSyntaxHighlighting();

        $this->assertSame([
            'blocks' => [
                ['type' => 'code', 'value' => 'echo 1;', 'highlight' => false],
                ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => false],
            ],
        ], $response['modal']->payload);
    }

    public function test_does_not_override_per_block_highlight_already_disabled(): void
    {
        $response = ModalResponse::stack([
            Block::code('x')->withoutHighlighting(),
            Block::json(['ok' => true])->withoutHighlighting(),
        ])->withoutSyntaxHighlighting();

        $this->assertSame([
            ['type' => 'code', 'value' => 'x', 'highlight' => false],
            ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => false],
        ], $response['modal']->payload['blocks']);
    }

    public function test_non_code_or_json_blocks_are_untouched(): void
    {
        $response = ModalResponse::stack([
            Block::text('hi'),
            Block::badge('Draft'),
            Block::divider(),
            Block::heading('Title'),
            Block::html('<b>x</b>'),
            Block::list(['a']),
        ])->withoutSyntaxHighlighting();

        $this->assertSame([
            'blocks' => [
                ['type' => 'text', 'value' => 'hi'],
                ['type' => 'badge', 'value' => 'Draft', 'variant' => 'default'],
                ['type' => 'divider'],
                ['type' => 'heading', 'value' => 'Title', 'size' => 'medium'],
                ['type' => 'html', 'value' => '<b>x</b>'],
                ['type' => 'list', 'value' => ['a'], 'ordered' => false],
            ],
        ], $response['modal']->payload);
    }

    public function test_mixed_stack_only_mutates_code_and_json(): void
    {
        $response = ModalResponse::stack([
            Block::text('intro'),
            Block::code('echo 1;'),
            Block::badge('x'),
            Block::json(['ok' => true]),
        ])->withoutSyntaxHighlighting();

        $this->assertSame([
            ['type' => 'text', 'value' => 'intro'],
            ['type' => 'code', 'value' => 'echo 1;', 'highlight' => false],
            ['type' => 'badge', 'value' => 'x', 'variant' => 'default'],
            ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => false],
        ], $response['modal']->payload['blocks']);
    }

    public function test_wire_carries_no_top_level_highlight_field(): void
    {
        $response = ModalResponse::stack([Block::code('echo 1;')])->withoutSyntaxHighlighting();

        $this->assertArrayNotHasKey('highlight', $response['modal']->payload);
    }

    public function test_legacy_modal_response_code_with_without_syntax_highlighting_produces_expected_wire(): void
    {
        $response = ModalResponse::code('echo 1;')->withoutSyntaxHighlighting();

        $this->assertSame([
            'blocks' => [
                ['type' => 'code', 'value' => 'echo 1;', 'highlight' => false],
            ],
        ], $response['modal']->payload);
    }
}
