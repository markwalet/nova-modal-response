<?php

namespace Markwalet\NovaModalResponse\Blocks;

use JsonException;

class JsonBlock extends Block
{
    private bool $highlight = true;

    private readonly string $encoded;

    /**
     * @param array<mixed> $value
     *
     * @throws JsonException
     */
    public function __construct(array $value)
    {
        $this->encoded = json_encode($value, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
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
        return [
            'type' => 'json',
            'value' => $this->encoded,
            'highlight' => $this->highlight,
        ];
    }
}
