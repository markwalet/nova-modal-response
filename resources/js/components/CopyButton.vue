<template>
    <button
        v-if="supported"
        type="button"
        @click.prevent="copy"
        class="p-1.5 rounded text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700"
        :title="copied ? 'Copied' : 'Copy'"
        dusk="copy-button"
    >
        <Icon :name="copied ? 'check' : 'clipboard'" class="w-4 h-4" />
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
