<template>
    <div :class="inline ? '' : 'py-3 px-8'">
        <Icon :name="block.value" :class="[variantClass, sizeClass]" />
    </div>
</template>

<script>
import { Icon } from 'laravel-nova-ui'

// Variant colours and sizes live in asset.css (.nmr-icon--*): Nova's compiled
// Tailwind lacks most coloured text-* utilities, and <Icon> forces w-6 h-6.
const VARIANTS = ['default', 'info', 'success', 'warning', 'danger']
const SIZES = ['small', 'medium', 'large']

export default {
    components: { Icon },

    props: {
        block: { type: Object, required: true },
        inline: { type: Boolean, default: false },
    },

    computed: {
        variantClass() {
            return `nmr-icon--${VARIANTS.includes(this.block.variant) ? this.block.variant : 'default'}`
        },
        sizeClass() {
            return `nmr-icon--${SIZES.includes(this.block.size) ? this.block.size : 'medium'}`
        },
    },
}
</script>
