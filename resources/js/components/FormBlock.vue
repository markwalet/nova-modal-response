<template>
    <div class="px-8 py-3">
        <form @submit.prevent="submit" autocomplete="off" dusk="form-block">
            <component
                v-for="field in fields"
                :is="'form-' + field.component"
                :key="field.attribute"
                :resource-name="block.resourceName"
                :field="field"
                :errors="errors"
                :show-help-text="field.helpText != null"
                @field-changed="handleDependsOnChange"
            />

            <div class="mt-4 flex justify-end">
                <Button
                    type="submit"
                    :loading="working"
                    :disabled="working"
                    dusk="form-block-submit"
                >
                    {{ block.value }}
                </Button>
            </div>
        </form>
    </div>
</template>

<script>
import { Button } from 'laravel-nova-ui'
import { handleDispatchResponse, surfaceDispatchError, makeErrors } from '../actionDispatch.js'

export default {
    components: { Button },

    inject: {
        modalResponseClose: { default: () => () => {} },
        modalResponseReplace: { default: () => () => {} },
    },

    props: {
        block: { type: Object, required: true },
    },

    data() {
        return {
            working: false,
            // Clone so the user's edits and depends-on refreshes don't mutate
            // the wire payload (which Vue freezes via prop reactivity anyway).
            fields: (this.block.fields ?? []).map(field => ({ ...field })),
            errors: makeErrors(),
        }
    },

    methods: {
        async submit() {
            if (this.working) {
                return
            }

            const resourceName = this.block.resourceName
            if (!resourceName) {
                Nova.error(
                    'Form block could not determine the origin resource. '
                    + 'Open the modal from a Nova resource action.'
                )
                return
            }

            this.errors.clear()
            this.working = true
            Nova.$progress.start()

            try {
                const response = await Nova.request({
                    method: 'post',
                    url: `/nova-api/${resourceName}/action`,
                    params: this.endpointParams(),
                    data: this.buildFormData(),
                })

                handleDispatchResponse(this.block, response.data, response.headers, {
                    close: this.modalResponseClose,
                    replace: this.modalResponseReplace,
                })
            } catch (error) {
                if (error && error.response && error.response.status === 422) {
                    // Map 422 validation errors back onto fields; leave the
                    // modal open so the user can correct and resubmit.
                    this.errors.record(error.response.data.errors ?? {})
                } else {
                    surfaceDispatchError(error, 'There was a problem submitting the form.')
                }
            } finally {
                this.working = false
                Nova.$progress.done()
            }
        },

        async handleDependsOnChange(attribute) {
            // Mirror ConfirmActionModal's depends-on round-trip: PATCH to the
            // same action endpoint (discriminated by HTTP verb), with the
            // changed component identified by `editing-mode` query params, and
            // the current field values as form data. Nova returns refreshed
            // field state; we splice each updated field back into place.
            const resourceName = this.block.resourceName
            if (!resourceName) {
                return
            }

            try {
                const { data } = await Nova.request({
                    method: 'patch',
                    url: `/nova-api/${resourceName}/action`,
                    params: {
                        ...this.endpointParams(),
                        editing: true,
                        editingMode: 'action',
                        component: attribute,
                    },
                    data: this.buildFormData(),
                })

                const next = Array.isArray(data) ? data : (data.fields ?? [])
                if (!Array.isArray(next) || next.length === 0) {
                    return
                }

                this.fields = this.fields.map(field => {
                    const updated = next.find(f => f.attribute === field.attribute)
                    return updated ? { ...field, ...updated } : field
                })
            } catch (error) {
                // Silent — a failed depends-on sync should not pop a toast or
                // close the modal; the form stays usable with stale state.
            }
        },

        endpointParams() {
            return { action: this.block.action }
        },

        buildFormData() {
            const formData = new FormData()
            const ids = Array.isArray(this.block.resources) ? this.block.resources : []
            for (const id of ids) {
                formData.append('resources[]', id)
            }

            for (const field of this.fields) {
                if (typeof field.fill === 'function') {
                    field.fill(formData)
                    continue
                }
                if (field.value !== undefined && field.value !== null) {
                    formData.append(field.attribute, field.value)
                }
            }

            return formData
        },
    },
}
</script>
