<?php

namespace Markwalet\NovaModalResponse\Blocks;

/**
 * The contract every block fulfils: it can serialize itself into the modal's
 * wire payload. Implementing this marks a class as a block and nothing more;
 * layout capability (e.g. sitting on an inline row) is a separate promise made
 * by the Inlineable interface.
 */
interface Renderable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
