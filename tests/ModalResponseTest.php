<?php

namespace Markwalet\NovaModalResponse\Tests;

use Laravel\Nova\Actions\Responses\Modal;
use Markwalet\NovaModalResponse\Blocks\Block;
use Markwalet\NovaModalResponse\ModalResponse;

class ModalResponseTest extends TestCase
{
    public function test_text_sugar_produces_a_single_text_block_stack(): void
    {
        $response = ModalResponse::text('Plain text');

        $this->assertSame([
            'blocks' => [
                ['type' => 'text', 'value' => 'Plain text'],
            ],
        ], $response['modal']->payload);
    }

    public function test_chrome_setters_serialise_to_the_expected_wire_keys(): void
    {
        $response = ModalResponse::stack([Block::text('body')])
            ->title('Snippet')
            ->size('7xl')
            ->closeButton('Done');

        $this->assertSame([
            'title' => 'Snippet',
            'size' => '7xl',
            'closeButtonText' => 'Done',
            'blocks' => [
                ['type' => 'text', 'value' => 'body'],
            ],
        ], $response['modal']->payload);
    }

    public function test_stack_with_a_closure_resolves_the_blocks(): void
    {
        $response = ModalResponse::stack(fn () => [Block::text('one'), Block::text('two')]);

        $this->assertSame([
            'blocks' => [
                ['type' => 'text', 'value' => 'one'],
                ['type' => 'text', 'value' => 'two'],
            ],
        ], $response['modal']->payload);
    }

    public function test_stack_with_an_empty_array_produces_an_empty_blocks_list(): void
    {
        $response = ModalResponse::stack([]);

        $this->assertSame(['blocks' => []], $response['modal']->payload);
    }

    public function test_stack_with_an_array_of_blocks_writes_the_blocks_wire_payload(): void
    {
        $response = ModalResponse::stack([Block::text('hi')]);

        $modal = $response['modal'];

        $this->assertInstanceOf(Modal::class, $modal);
        $this->assertSame('modal-response', $modal->component);
        $this->assertSame([
            'blocks' => [
                ['type' => 'text', 'value' => 'hi'],
            ],
        ], $modal->payload);
    }
}
