<?php

namespace Markwalet\NovaModalResponse\Blocks;

use InvalidArgumentException;
use Markwalet\NovaModalResponse\Block;

class TabsBlock implements Renderable
{
    /** @var list<Tab> */
    private readonly array $tabs;

    /**
     * @param array<mixed> $tabs
     */
    public function __construct(array $tabs)
    {
        $normalized = [];

        foreach ($tabs as $key => $value) {
            if ($value instanceof Tab) {
                $normalized[] = $value;

                continue;
            }

            if (is_string($key) && is_array($value)) {
                $normalized[] = Block::tab($key, $value);

                continue;
            }

            throw new InvalidArgumentException('a tab needs a label');
        }

        $this->tabs = $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'tabs',
            'value' => array_map(static fn (Tab $tab): array => $tab->toArray(), $this->tabs),
        ];
    }
}
