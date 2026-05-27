<?php

namespace Markwalet\NovaModalResponse\Blocks\Concerns;

/**
 * An icon rendered *inside* a host block's chrome (button pill, badge, link),
 * sharing its background, padding and foreground colour — not a standalone atom
 * beside it. Leading by default; embedded icons carry no variant of their own,
 * they inherit the host's colour. Used by link and badge blocks.
 */
trait HasEmbeddedIcon
{
    private ?string $icon = null;

    private string $iconPosition = 'leading';

    public function icon(string $name, bool $trailing = false): self
    {
        $this->icon = $name;
        $this->iconPosition = $trailing ? 'trailing' : 'leading';

        return $this;
    }

    /**
     * The two wire-format keys every host emits, always present.
     *
     * @return array{icon: string|null, iconPosition: string}
     */
    private function iconAttributes(): array
    {
        return [
            'icon' => $this->icon,
            'iconPosition' => $this->iconPosition,
        ];
    }
}
