<template>
    <div class="py-3 px-8">
        <Icon :name="block.value" :class="variantClass" class="w-5 h-5" />
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

export default {
    components: { Icon },

    props: {
        block: { type: Object, required: true },
    },

    computed: {
        variantClass() {
            return VARIANT_CLASSES[this.block.variant] ?? VARIANT_CLASSES.default
        },
    },
}
</script>
