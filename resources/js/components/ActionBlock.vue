<template>
    <div class="py-3 px-8">
        <Button
            :loading="working"
            :disabled="working"
            @click.prevent="dispatchAction"
            dusk="action-block-button"
        >
            {{ block.value }}
        </Button>
    </div>
</template>

<script>
import { Button } from 'laravel-nova-ui'
import { handleDispatchResponse, surfaceDispatchError } from '../actionDispatch.js'

export default {
    components: { Button },

    inject: {
        // Provided by ModalActionResponse. `close` shuts the current modal;
        // `replace` swaps the current modal's payload with a new one
        // (close-then-open in a single step — one modal visible at any time).
        modalResponseClose: { default: () => () => {} },
        modalResponseReplace: { default: () => () => {} },
    },

    props: {
        block: { type: Object, required: true },
    },

    data() {
        return {
            working: false,
        }
    },

    methods: {
        async dispatchAction() {
            if (this.working) {
                return
            }

            // Confirmation guard. Nova's action runner shows a confirm modal
            // before POSTing; intercepting bypasses it, so honor the same
            // contract here. `confirmText` is null when no prompt is wanted.
            // A native browser confirm keeps the "one modal at a time" rule
            // (it's not a Nova modal stacked on top of ours).
            if (this.block.confirmText) {
                const ok = await this.confirmDispatch(this.block.confirmText)
                if (!ok) {
                    return
                }
            }

            const resourceName = this.block.resourceName
            if (!resourceName) {
                Nova.error(
                    'Action block could not determine the origin resource. '
                    + 'Open the modal from a Nova resource action.'
                )
                return
            }

            const url = `/nova-api/${resourceName}/action`
            const params = { action: this.block.action }

            const formData = new FormData()
            const ids = Array.isArray(this.block.resources) ? this.block.resources : []
            for (const id of ids) {
                formData.append('resources[]', id)
            }

            this.working = true
            Nova.$progress.start()

            try {
                const response = await Nova.request({
                    method: 'post',
                    url,
                    params,
                    data: formData,
                })

                handleDispatchResponse(this.block, response.data, response.headers, {
                    close: this.modalResponseClose,
                    replace: this.modalResponseReplace,
                })
            } catch (error) {
                surfaceDispatchError(error)
            } finally {
                this.working = false
                Nova.$progress.done()
            }
        },

        confirmDispatch(text) {
            // Indirection point: tests stub this to simulate confirm/cancel
            // without invoking the browser dialog.
            return Promise.resolve(window.confirm(text))
        },
    },
}
</script>
