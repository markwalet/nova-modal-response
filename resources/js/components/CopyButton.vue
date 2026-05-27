<template>
    <button
        v-if="supported"
        type="button"
        @click.prevent="copy"
        class="nmr-copy-button"
        :title="copied ? 'Copied' : 'Copy'"
        dusk="copy-button"
    >
        <Icon :name="copied ? 'check' : 'clipboard'" />
    </button>
</template>

<script>
import { Icon } from 'laravel-nova-ui'

export default {
    components: { Icon },

    props: {
        value: { type: String, required: true },
    },

    data() {
        return {
            copied: false,
            resetTimeout: null,
        }
    },

    computed: {
        supported() {
            return typeof navigator !== 'undefined' && !!navigator.clipboard
        },
    },

    mounted() {
        if (! this.supported) {
            console.warn('[nova-modal-response] Copy button hidden: clipboard API unavailable (requires a secure context such as HTTPS or localhost).')
        }
    },

    methods: {
        async copy() {
            await navigator.clipboard.writeText(this.value)
            this.copied = true
            clearTimeout(this.resetTimeout)
            this.resetTimeout = setTimeout(() => { this.copied = false }, 2000)
        },
    },

    beforeUnmount() {
        clearTimeout(this.resetTimeout)
    },
}
</script>
