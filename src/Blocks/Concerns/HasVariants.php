<?php

namespace Markwalet\NovaModalResponse\Blocks\Concerns;

use Markwalet\NovaModalResponse\Enums\Variant;

/**
 * The five shared semantic colour variants, exposed as zero-arg fluent methods.
 * Used by every block that carries a `variant` (badge, icon); `default` is what
 * you get if no variant method is called.
 */
trait HasVariants
{
    private Variant $variant = Variant::DEFAULT;

    public function default(): self
    {
        return $this->variant(Variant::DEFAULT);
    }

    public function info(): self
    {
        return $this->variant(Variant::INFO);
    }

    public function success(): self
    {
        return $this->variant(Variant::SUCCESS);
    }

    public function warning(): self
    {
        return $this->variant(Variant::WARNING);
    }

    public function danger(): self
    {
        return $this->variant(Variant::DANGER);
    }

    public function variant(string|Variant $variant): self
    {
        $this->variant = $variant instanceof Variant
            ? $variant
            : Variant::from($variant);

        return $this;
    }
}
