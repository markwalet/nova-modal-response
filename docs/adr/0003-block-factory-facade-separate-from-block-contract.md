# Block factory facade is separate from the block contract

## Context

Through v2.0 a single class, `Markwalet\NovaModalResponse\Blocks\Block`, did two
unrelated jobs at once:

1. It was the **abstract base class** every concrete block extended
   (`BadgeBlock extends Block`), defining the `toArray()` contract.
2. It was the **static factory hub** for every block type (`Block::text()`,
   `Block::badge()`, `Block::code()`, …) plus the `Block::normalize()` coercion
   helper.

That double duty caused a name collision that blocked future API work. Because
every block inherited the static factories, a concrete block could not declare
an instance method whose name matched a factory — e.g. a future
`Block::badge()->icon('warning')` (a badge carrying an icon) clashed with the
inherited static `icon()` factory. The name `Block::` was also doing too much:
it read as both "the type a block is" and "the thing you call to make one".

## Decision

Split the two responsibilities into two types, and relocate the factory next to
`ModalResponse` in the main namespace:

- **`Markwalet\NovaModalResponse\Blocks\Renderable`** — an **interface** (not an
  abstract class) that is the block contract: `toArray(): array`. Every concrete
  block `implements Renderable`. There is no abstract base class; blocks shared
  no behaviour, so a marker-style contract is enough and mirrors the existing
  `Inlineable` interface.
- **`Markwalet\NovaModalResponse\Block`** — a `final` static factory facade in
  the main namespace, keeping the high-traffic name `Block`. It holds every
  block factory and `Block::normalize()` (coercion belongs with construction).
  It builds blocks but is **not** itself a block.

Concrete blocks now `implements Renderable` (and `Inlineable` where they are
atoms). Type hints that meant "a block" (`ModalResponse`, `PayloadBuilder`,
`InlineBlock`, `CollapsibleBlock`) reference `Renderable`. Inside the `Blocks\`
namespace, code that calls the factory imports the root `Block`.

This is a breaking change to the public authoring surface (the `Block` import
path moves; the abstract base is gone) — acceptable as part of the v2 cleanup
before the wire format and docs stabilise.

## Considered alternatives

- **Keep the factory on the abstract base (status quo).** Rejected: it is the
  collision above — every block inherits the static factories, so block instance
  methods can never share a name with a factory.
- **Abstract class `AbstractBlock` instead of an interface.** Rejected: blocks
  share no implementation (no `parent::` calls, no common state), so an abstract
  class buys nothing today and spends the single-inheritance slot. An interface
  is more flexible and matches `Inlineable`. (YAGNI; revisit only if real shared
  behaviour appears.)
- **Give the factory a distinct name (`Content`, `Brick`, `Make`, …) and keep
  `Block` as the base type.** Rejected: `Block::badge()` is the high-traffic,
  user-facing call and reads best with the name `Block`; the base type is rarely
  named by users. The name is better spent on the facade. The interface took the
  intent-named `Renderable` instead.
- **A second `Stackable` marker interface** (mirroring `Inlineable`) so root vs
  inline capability is symmetric. Rejected for now: nothing is inline-only today
  (every atom is also valid at root), so the marker would gate nothing, and a
  runtime `instanceof` rejection on an untyped `stack()` array punishes at run
  time rather than guiding at authoring time. Guidance lives in docs instead.
  Revisit only when a block is genuinely broken (not merely unusual) at root.

## Consequences

- Custom-block authors implement `Renderable` (one method) instead of extending
  a base class; nothing forces a second interface.
- The collision is gone: a concrete block may define any instance method,
  including names that match a factory (`->icon()`, `->link()`, …).
- `Block::normalize()` lives on the facade; `InlineBlock`/`CollapsibleBlock`
  import the root `Block` to call it.
- Adding a block type stays additive: a new `Renderable` class + a factory
  method on `Block` + a Vue component + a component-map entry (per ADR-0001).
