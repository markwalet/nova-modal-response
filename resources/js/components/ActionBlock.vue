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
            // Intercept the click rather than defer to Nova's action runner —
            // deferring forfeits the close-then-open sequencing the form block
            // also needs. Nova stacks modals; we don't.
            if (this.working) {
                return
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

                this.handleResponse(response.data, response.headers)
            } catch (error) {
                this.surfaceError(error)
            } finally {
                this.working = false
                Nova.$progress.done()
            }
        },

        handleResponse(data, headers) {
            // Modal response: replace the current modal with the new payload.
            // The current modal unmounts; the new one renders in its place.
            if (data && data.modal && data.modal.payload) {
                Nova.$emit('action-executed')
                this.modalResponseReplace(data.modal.payload)
                return
            }

            // Non-modal response: delegate to Nova's standard handling, then
            // close the current modal. Always closes — no stay-open exception.
            this.delegateToNova(data, headers)
            this.modalResponseClose()
        },

        delegateToNova(data, headers) {
            if (!data) {
                Nova.$emit('action-executed')
                return
            }

            if (data.event) {
                Nova.$emit(data.event.key, data.event.payload)
            }

            if (data.download) {
                Nova.$emit('action-executed')
                this.showMessage(data)
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
                this.showMessage(data)
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
                this.showMessage(data)
                Nova.visit({
                    url: Nova.url(data.visit.path, data.visit.options),
                    remote: false,
                })
                return
            }

            Nova.$emit('action-executed')
            this.showMessage(data)
        },

        showMessage(data) {
            if (data && data.danger) {
                Nova.error(data.danger)
                return
            }
            if (data && data.message) {
                Nova.success(data.message)
            }
        },

        surfaceError(error) {
            // Surface the error and leave the modal open so the user can retry.
            const message = (error && error.response && error.response.data && error.response.data.message)
                || (error && error.message)
                || 'There was a problem dispatching the action.'

            Nova.error(message)
        },
    },
}
</script>
