<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Stringable;

class MarkdownBlock extends Block
{
    public function __construct(
        private readonly string|Stringable|null $content = null,
        private readonly ?string $file = null,
    ) {
        if (($this->content === null) === ($this->file === null)) {
            throw new InvalidArgumentException(
                'A markdown block requires exactly one source: pass either content or file, not both or neither.',
            );
        }
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
        if ($this->content !== null) {
            return (string) $this->content;
        }

        if (! is_file($this->file) || ($contents = @file_get_contents($this->file)) === false) {
            throw new InvalidArgumentException("Unable to read markdown file [{$this->file}].");
        }

        return $contents;
    }
}
