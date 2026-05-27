<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

class HeadingBlock implements Renderable
{
    private string $size = 'medium';

    public function __construct(private readonly string|Stringable $value) {}

    public function small(): self
    {
        $this->size = 'small';

        return $this;
    }

    public function medium(): self
    {
        $this->size = 'medium';

        return $this;
    }

    public function large(): self
    {
        $this->size = 'large';

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'heading',
            'value' => (string) $this->value,
            'size' => $this->size,
        ];
    }
}
