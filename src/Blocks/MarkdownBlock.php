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
            'value' => Str::markdown($this->source()),
        ];
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
