<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

class LinkBlock extends Block implements Inlineable
{
    private bool $newTab = false;

    private string $appearance = 'link';

    public function __construct(
        private readonly string|Stringable $label,
        private readonly string|Stringable $href,
    ) {}

    public function newTab(): self
    {
        $this->newTab = true;

        return $this;
    }

    public function button(): self
    {
        $this->appearance = 'button';

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'link',
            'value' => (string) $this->label,
            'href' => (string) $this->href,
            'appearance' => $this->appearance,
            'newTab' => $this->newTab,
        ];
    }
}
