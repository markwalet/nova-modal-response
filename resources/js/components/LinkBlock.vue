<template>
    <div class="py-3 px-8">
        <a
            :href="block.href"
            :target="block.newTab ? '_blank' : null"
            :rel="block.newTab ? 'noopener noreferrer' : null"
            :class="[appearanceClass, sizeClass]"
            class="inline-flex items-center gap-1"
        >
            <Icon v-if="block.icon && block.iconPosition === 'leading'" :name="block.icon" type="micro" />
            <span v-text="block.value" />
            <Icon v-if="block.icon && block.iconPosition === 'trailing'" :name="block.icon" type="micro" />
        </a>
    </div>
</template>

<script>
import { Icon } from 'laravel-nova-ui'

const APPEARANCE_CLASSES = {
    link: 'text-primary-500 font-bold hover:text-primary-400',
    button: 'px-3 py-1 rounded font-bold no-underline ' +
        'bg-primary-500 text-white hover:bg-primary-400 active:bg-primary-600',
}

const SIZE_CLASSES = {
    small: 'text-sm',
    medium: '',
    large: 'text-lg',
}

export default {
    components: { Icon },

    props: {
        block: { type: Object, required: true },
    },

    computed: {
        appearanceClass() {
            return APPEARANCE_CLASSES[this.block.appearance] ?? APPEARANCE_CLASSES.link
        },
        sizeClass() {
            return SIZE_CLASSES[this.block.size] ?? SIZE_CLASSES.medium
        },
    },
}
</script>
