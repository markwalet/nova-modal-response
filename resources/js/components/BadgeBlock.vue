<template>
    <div class="py-3 px-8">
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

const VARIANT_CLASSES = {
    default: 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    info: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    success: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    danger: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
}

const SIZE_CLASSES = {
    small: 'text-xs px-1.5 py-0.5',
    medium: 'text-sm px-2 py-0.5',
    large: 'text-lg px-3 py-1.5',
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
