<?php

namespace Markwalet\NovaModalResponse\Blocks\Concerns;

use Markwalet\NovaModalResponse\Enums\Size;

/**
 * The three shared visual sizes, exposed as zero-arg fluent methods.
 * Used by every block that carries a `size` (heading, badge, icon, link);
 * `medium` is what you get if no size method is called.
 */
trait HasSize
{
    private Size $size = Size::MEDIUM;

    public function small(): self
    {
        return $this->size(Size::SMALL);
    }

    public function medium(): self
    {
        return $this->size(Size::MEDIUM);
    }

    public function large(): self
    {
        return $this->size(Size::LARGE);
    }

    public function size(string|Size $size): self
    {
        $this->size = $size instanceof Size
            ? $size
            : Size::from($size);

        return $this;
    }
}
