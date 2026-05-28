# Intercept action dispatch rather than defer to Nova's action runner

## Context

The action block (and the planned form block) turns the modal response from a
one-shot render into an interactive round-trip: a button inside the modal
dispatches a Nova action, and whatever the action returns drives what happens
next — a toast, a redirect, a download, or another modal stacked over the
current one.

There are two ways to make a button inside our modal run a Nova action:

1. **Defer**: hand the click off to Nova's own action runner
   (`useActions().handleActionClick(uriKey)`), the same code path Nova uses
   from the index/detail action dropdown.
2. **Intercept**: POST to Nova's action endpoint ourselves
   (`/nova-api/{resource}/action?action={uriKey}`) and own the response.

Deferring is the smaller change — it reuses Nova's existing dispatch — but it
forfeits two things the interactive flow needs:

- **In-place re-render.** A `modal-response` payload coming back from a button
  inside the modal must render as a *child* modal over the parent (the
  `inChildModal` disposition, our default) or *replace* the current stack in
  place (`inPlace`, planned). Nova's runner only knows one disposition:
  unconditionally open a new response modal at the top of the stack. There is
  no hook to say "render this one over that one" or "swap this stack for
  that one".
- **Disposition control.** The non-modal cases (toast, redirect, download,
  openInNewTab) are followed in our flow by either *close the parent modal*
  (the default) or *keep it open* (`->stayOpen()`). Deferring runs Nova's
  default post-action behaviour (refresh + toast) and leaves our modal alone;
  there is no signal we can attach the close/stay-open decision to.

## Decision

The Vue side **intercepts** the click rather than deferring to Nova's runner.
`ActionBlock.vue` POSTs to `/nova-api/{resourceName}/action` itself, carrying
the **origin context** (the parent modal's `resourceName` + selected
`resources` ids, captured server-side at serialize time and shipped on the
wire), shows a loading/disabled state on the button while the request is in
flight, and then drives the outcome itself:

- If the payload carries `modal.payload` (our `modal-response` shape), open it
  as a child `ModalActionResponse` stacked over this one (`inject`ed
  `modalResponseOpenChild`). The parent stays mounted underneath; closing the
  child returns to it.
- Otherwise it is a non-modal response: walk the same response shapes
  Nova's runner walks (`event`, `download`, `deleted`, `redirect`, `visit`,
  message/danger toast) and emit `action-executed` so detail/index views
  refresh, then close the parent modal — unless the block was configured with
  `->stayOpen()`, in which case it stays open.
- A failed dispatch surfaces an error via `Nova.error()` and leaves the modal
  open so the user can retry.

The same dispatch core is shared with the future form block; intercepting is
what makes "submit fields and render the result inline" possible at all.

## Considered alternatives

- **Defer to Nova's action runner.** Rejected for the reasons above — no
  in-place re-render, no disposition control, the close/stay-open decision
  has nowhere to live. The smaller change cost is not worth the loss of
  control over the interactive flow's two distinguishing behaviours.
- **A bespoke server endpoint of our own** that wraps Nova's action endpoint
  to inject disposition handling. Rejected: it duplicates Nova's
  authorisation, validation and resource lookup, and provides no benefit
  over POSTing to Nova's existing endpoint from the Vue side. Nova's endpoint
  still enforces `authorizedToRun` + validation when we POST to it, so we
  inherit security without re-implementing it.
- **Extend `useActions` upstream** to take a disposition argument. Rejected:
  not ours to change, and even if it were, the parent/child stacking logic
  is package-specific — it does not belong in Nova core.

## Consequences

- The package owns the dispatch loop end-to-end for action and form blocks,
  so disposition (`->inChildModal()` default, `->inPlace()` planned) and
  post-response behaviour (close vs `->stayOpen()`) are knobs we control.
- Security is unchanged: Nova's action endpoint still authorises and
  validates every dispatch. The origin context that rides the payload is
  tamperable, but the endpoint enforces `authorizedToRun` against whatever
  resource + ids ultimately reach it — no privilege escalation.
- Confirmation guards (`confirmText`, `->confirm()`) and resource-reload
  opt-outs (`->withoutReload()`) are ours to wire in this flow; the first
  slice ships dispatch + disposition + close/stay-open, leaving those
  knobs for follow-up tickets.
- `ModalActionResponse.vue` now self-recurses for the child-modal case via
  the `name: 'ModalActionResponse'` self-reference — one render path still,
  just stacked.
