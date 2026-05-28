<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

class CodeBlock implements Renderable
{
    private ?string $language = null;

    private bool $highlight = true;

    public function __construct(private readonly string|Stringable $value) {}

    public function language(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function withoutHighlighting(): self
    {
        $this->highlight = false;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'type' => 'code',
            'value' => (string) $this->value,
            'highlight' => $this->highlight,
        ];

        if ($this->language !== null) {
            $payload['language'] = $this->language;
        }

        return $payload;
    }
}
