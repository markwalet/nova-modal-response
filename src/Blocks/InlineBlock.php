<?php

namespace Markwalet\NovaModalResponse\Blocks;

use InvalidArgumentException;
use Markwalet\NovaModalResponse\Enums\Alignment;
use Stringable;

class InlineBlock extends Block
{
    private Alignment $alignment = Alignment::DEFAULT;

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
        return $this->alignment(Alignment::SPREAD);
    }

    public function center(): self
    {
        return $this->alignment(Alignment::CENTER);
    }

    public function end(): self
    {
        return $this->alignment(Alignment::END);
    }

    public function alignment(string|Alignment $alignment): self
    {
        $this->alignment = $alignment instanceof Alignment
            ? $alignment
            : Alignment::from($alignment);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'inline',
            'alignment' => $this->alignment->value,
            'value' => array_map(static fn (Block $atom): array => $atom->toArray(), $this->atoms),
        ];
    }
}
