// Shared dispatch core for the action block (fieldless) and the form block
// (fieldful). Both POST to Nova's action endpoint themselves rather than
// deferring to Nova's runner — owning the response is what lets us apply the
// close-then-open rule (one modal visible at a time).

export function handleDispatchResponse(block, data, headers, { close, replace }) {
    reloadResourceView(block)

    // Modal response: replace the current modal with the new payload. The
    // current modal unmounts; the new one renders in its place.
    if (data && data.modal && data.modal.payload) {
        Nova.$emit('action-executed')
        replace(data.modal.payload)
        return
    }

    // Non-modal response: delegate to Nova's standard handling, then close
    // the current modal. Always closes — no stay-open exception.
    delegateToNova(data, headers)
    close()
}

function reloadResourceView(block) {
    // Restore Nova's native "the resource view refreshes after an action
    // runs" behavior — the interception bypasses Nova's runner, which is
    // what otherwise emits this on the Index/Lens view. `block.reload ===
    // false` opts out for read-only / preview actions (`->withoutReload()`).
    if (block && block.reload === false) {
        return
    }

    Nova.$emit('refresh-resources')
}

function delegateToNova(data, headers) {
    if (!data) {
        Nova.$emit('action-executed')
        return
    }

    if (data.event) {
        Nova.$emit(data.event.key, data.event.payload)
    }

    if (data.download) {
        Nova.$emit('action-executed')
        showMessage(data)
        const link = document.createElement('a')
        link.href = data.download.url
        link.download = data.download.name
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        return
    }

    if (data.deleted) {
        Nova.$emit('action-executed')
        showMessage(data)
        return
    }

    if (data.redirect) {
        if (data.redirect.openInNewTab) {
            Nova.$emit('action-executed')
            window.open(data.redirect.url, '_blank')
            return
        }
        window.location = data.redirect.url
        return
    }

    if (data.visit) {
        showMessage(data)
        Nova.visit({
            url: Nova.url(data.visit.path, data.visit.options),
            remote: false,
        })
        return
    }

    Nova.$emit('action-executed')
    showMessage(data)
}

function showMessage(data) {
    if (data && data.danger) {
        Nova.error(data.danger)
        return
    }
    if (data && data.message) {
        Nova.success(data.message)
    }
}

export function surfaceDispatchError(error, fallback = 'There was a problem dispatching the action.') {
    const message = (error && error.response && error.response.data && error.response.data.message)
        || (error && error.message)
        || fallback

    Nova.error(message)
}

// Minimal Errors shim matching the surface Nova field components consume from
// `form-backend-validation`: has(field), first(field), get(field), any(). The
// package doesn't ship that dep — this covers what the form block needs.
export function makeErrors(initial = {}) {
    let bag = { ...initial }
    return {
        record(next) { bag = { ...next } },
        clear() { bag = {} },
        any() { return Object.keys(bag).length > 0 },
        has(field) { return Object.prototype.hasOwnProperty.call(bag, field) },
        first(field) {
            const list = bag[field]
            return Array.isArray(list) ? list[0] : list
        },
        get(field) {
            const list = bag[field]
            return Array.isArray(list) ? list : (list ? [list] : [])
        },
    }
}
