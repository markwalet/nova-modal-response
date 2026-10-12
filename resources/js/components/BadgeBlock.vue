<template>
    <div :class="inline ? 'flex' : 'py-3 px-8'">
        <span :class="[variantClass, sizeClass]"
              class="inline-flex items-center gap-1 rounded font-bold">
            <Icon v-if="block.icon && block.iconPosition === 'leading'" :name="block.icon" type="micro" />
            <span v-text="block.value" />
            <Icon v-if="block.icon && block.iconPosition === 'trailing'" :name="block.icon" type="micro" />
        </span>
    </div>
</template>

<script>
import { Icon } from 'laravel-nova-ui'

// Variant colours live in asset.css (.nmr-badge--*): Nova's compiled Tailwind
// lacks most of the coloured text-* and dark:* utilities.
const VARIANTS = ['default', 'info', 'success', 'warning', 'danger']

const SIZE_CLASSES = {
    small: 'text-xs px-3 py-1',
    medium: 'text-sm px-3 py-1',
    large: 'text-lg px-3 py-1',
}

export default {
    components: { Icon },

    props: {
        block: { type: Object, required: true },
        inline: { type: Boolean, default: false },
    },

    computed: {
        variantClass() {
            return `nmr-badge--${VARIANTS.includes(this.block.variant) ? this.block.variant : 'default'}`
        },
        sizeClass() {
            return SIZE_CLASSES[this.block.size] ?? SIZE_CLASSES.medium
        },
    },
}
</script>
