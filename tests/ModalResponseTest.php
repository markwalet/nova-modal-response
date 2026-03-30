<?php

namespace Markwalet\NovaModalResponse\Tests;

use JsonException;
use Laravel\Nova\Actions\Responses\Modal;
use Markwalet\NovaModalResponse\ModalResponse;

class ModalResponseTest extends TestCase
{
    public function test_code_response_builds_the_expected_modal_payload(): void
    {
        $response = ModalResponse::code('echo "test";')
            ->title('Snippet')
            ->size('7xl')
            ->withoutSyntaxHighlighting()
            ->closeButton('Done');

        $modal = $response['modal'];

        $this->assertInstanceOf(Modal::class, $modal);
        $this->assertSame('modal-response', $modal->component);
        $this->assertSame([
            'code' => 'echo "test";',
            'title' => 'Snippet',
            'size' => '7xl',
            'highlight' => false,
            'closeButtonText' => 'Done',
        ], $modal->payload);
    }

    public function test_json_response_pretty_prints_the_payload(): void
    {
        $response = ModalResponse::json([
            'lorem' => 'ipsum',
            'dolor' => ['sit', 'amet'],
        ]);

        $modal = $response['modal'];

        $this->assertInstanceOf(Modal::class, $modal);
        $this->assertSame([
            'code' => json_encode([
                'lorem' => 'ipsum',
                'dolor' => ['sit', 'amet'],
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
        ], $modal->payload);
    }

    public function test_json_response_throws_for_invalid_utf8(): void
    {
        $this->expectException(JsonException::class);

        ModalResponse::json([
            'invalid' => "\xB1\x31",
        ]);
    }

    public function test_text_and_html_responses_use_the_expected_payload_keys(): void
    {
        $textResponse = ModalResponse::text('Plain text');
        $htmlResponse = ModalResponse::html('<strong>HTML</strong>');

        $this->assertSame([
            'body' => 'Plain text',
        ], $textResponse['modal']->payload);

        $this->assertSame([
            'html' => '<strong>HTML</strong>',
        ], $htmlResponse['modal']->payload);
    }
}
