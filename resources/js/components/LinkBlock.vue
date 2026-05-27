<template>
    <div class="py-3 px-8">
        <a
            :href="block.href"
            :target="block.newTab ? '_blank' : null"
            :rel="block.newTab ? 'noopener noreferrer' : null"
            :class="appearanceClass"
            v-text="block.value"
        />
    </div>
</template>

<script>
const APPEARANCE_CLASSES = {
    link: 'text-primary-500 font-bold hover:text-primary-400',
    button: 'inline-flex items-center px-3 py-1 rounded font-bold no-underline ' +
        'bg-primary-500 text-white hover:bg-primary-400 active:bg-primary-600',
}

export default {
    props: {
        block: { type: Object, required: true },
    },

    computed: {
        appearanceClass() {
            return APPEARANCE_CLASSES[this.block.appearance] ?? APPEARANCE_CLASSES.link
        },
    },
}
</script>
