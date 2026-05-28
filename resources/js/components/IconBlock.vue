<template>
    <div class="py-3 px-8">
        <Icon :name="block.value" :class="[variantClass, sizeClass]" />
    </div>
</template>

<script>
import { Icon } from 'laravel-nova-ui'

const VARIANT_CLASSES = {
    default: 'text-gray-500 dark:text-gray-400',
    info: 'text-blue-500 dark:text-blue-400',
    success: 'text-green-500 dark:text-green-400',
    warning: 'text-yellow-500 dark:text-yellow-400',
    danger: 'text-red-500 dark:text-red-400',
}

const SIZE_CLASSES = {
    small: 'w-4 h-4',
    medium: 'w-5 h-5',
    large: 'w-6 h-6',
}

export default {
    components: { Icon },

    props: {
        block: { type: Object, required: true },
    },

    computed: {
        variantClass() {
            return VARIANT_CLASSES[this.block.variant] ?? VARIANT_CLASSES.default
        },
        sizeClass() {
            return SIZE_CLASSES[this.block.size] ?? SIZE_CLASSES.medium
        },
    },
}
</script>
