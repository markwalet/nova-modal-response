<?php

namespace Markwalet\NovaModalResponse\Blocks;

/**
 * Marks a block as an inline atom: a block that may sit inside an inline group
 * and render on a single horizontal row. Implementing this interface is a
 * promise about layout only; it does not change a block's serialized output.
 */
interface Inlineable {}
