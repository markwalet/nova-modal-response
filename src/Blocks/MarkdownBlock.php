<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Stringable;

class MarkdownBlock extends Block
{
    private bool $isFile = false;

    public function __construct(
        private readonly string|Stringable $content,
    ) {}

    /**
     * Treat the content as a filesystem path to a markdown file.
     */
    public function file(): self
    {
        $this->isFile = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'html',
            'value' => $this->wrap(Str::markdown($this->source())),
        ];
    }

    /**
     * Wrap the compiled HTML in a markdown-scoped container so it can be styled
     * to match the native blocks without affecting raw `html` blocks. The block
     * still emits `type: html`; only the value carries the wrapper (see ADR-0002).
     */
    private function wrap(string $html): string
    {
        return '<div class="modal-response-markdown">'.$html.'</div>';
    }

    private function source(): string
    {
        if (! $this->isFile) {
            return (string) $this->content;
        }

        $path = (string) $this->content;

        if (! is_file($path) || ($contents = @file_get_contents($path)) === false) {
            throw new InvalidArgumentException("Unable to read markdown file [{$path}].");
        }

        return $contents;
    }
}
