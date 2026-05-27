<?php

namespace Markwalet\NovaModalResponse\Tests\Blocks;

use InvalidArgumentException;
use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\Blocks\MarkdownBlock;
use Markwalet\NovaModalResponse\ModalResponse;
use Markwalet\NovaModalResponse\Tests\TestCase;

class MarkdownBlockTest extends TestCase
{
    public function test_factory_returns_a_markdown_block(): void
    {
        $this->assertInstanceOf(MarkdownBlock::class, Block::markdown('# Hi'));
    }

    public function test_it_compiles_inline_content_to_an_html_block(): void
    {
        $block = Block::markdown('# Title');

        $result = $block->toArray();

        $this->assertSame('html', $result['type']);
        $this->assertStringContainsString('<h1>Title</h1>', $result['value']);
    }

    public function test_it_wraps_the_compiled_html_in_a_markdown_scoped_container(): void
    {
        $block = Block::markdown('# Title');

        $result = $block->toArray();

        // Still an html block on the wire (ADR-0002) — only the value is wrapped
        // so the markdown-scoped stylesheet can style it without touching raw
        // html blocks.
        $this->assertSame('html', $result['type']);
        $this->assertSame(
            '<div class="modal-response-markdown"><h1>Title</h1>'."\n".'</div>',
            $result['value'],
        );
    }

    public function test_it_compiles_a_file_to_an_html_block(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'md').'.md';
        file_put_contents($path, "# From file\n\nSome **bold** text.");

        try {
            $block = Block::markdown($path)->file();

            $result = $block->toArray();

            $this->assertSame('html', $result['type']);
            $this->assertStringContainsString('<h1>From file</h1>', $result['value']);
            $this->assertStringContainsString('<strong>bold</strong>', $result['value']);
        } finally {
            @unlink($path);
        }
    }

    public function test_a_missing_file_throws_at_serialize(): void
    {
        $block = Block::markdown('/does/not/exist.md')->file();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to read markdown file');

        $block->toArray();
    }

    public function test_markdown_sugar_produces_a_single_html_block_stack(): void
    {
        $response = ModalResponse::markdown('# Title');

        $this->assertSame([
            'blocks' => [
                ['type' => 'html', 'value' => '<div class="modal-response-markdown"><h1>Title</h1>'."\n".'</div>'],
            ],
        ], $response['modal']->payload);
    }
}
