<?php

namespace Markwalet\NovaModalResponse\Blocks;

use InvalidArgumentException;
use Stringable;

class InlineBlock extends Block
{
    private bool $spread = false;

    /** @var list<Block&Inlineable> */
    private readonly array $atoms;

    /**
     * @param array<int, Block|string|Stringable> $atoms
     */
    public function __construct(array $atoms)
    {
        $validated = [];

        foreach ($atoms as $atom) {
            $atom = Block::normalize($atom);

            if (! $atom instanceof Inlineable) {
                throw new InvalidArgumentException('Inline group may only contain Inlineable atoms.');
            }

            $validated[] = $atom;
        }

        $this->atoms = $validated;
    }

    public function spread(): self
    {
        $this->spread = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'inline',
            'spread' => $this->spread,
            'value' => array_map(static fn (Block $atom): array => $atom->toArray(), $this->atoms),
        ];
    }
}
