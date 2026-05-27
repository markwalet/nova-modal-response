<?php

namespace Markwalet\NovaModalResponse\Blocks\Concerns;

/**
 * The five shared semantic colour variants, exposed as zero-arg fluent methods.
 * Used by every block that carries a `variant` (badge, icon); `default` is what
 * you get if no variant method is called.
 */
trait HasVariants
{
    private string $variant = 'default';

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

    private function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }
}
