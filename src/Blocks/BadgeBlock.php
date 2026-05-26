<?php

namespace Markwalet\NovaModalResponse\Blocks;

use Illuminate\Support\Stringable;

class BadgeBlock extends Block
{
    private string $variant = 'default';

    public function __construct(private readonly string|Stringable $value) {}

    public function default(): self
    {
        return $this->setVariant('default');
    }

    public function info(): self
    {
        return $this->setVariant('info');
    }

    public function success(): self
    {
        return $this->setVariant('success');
    }

    public function warning(): self
    {
        return $this->setVariant('warning');
    }

    public function danger(): self
    {
        return $this->setVariant('danger');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'badge',
            'value' => (string) $this->value,
            'variant' => $this->variant,
        ];
    }

    private function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }
}
