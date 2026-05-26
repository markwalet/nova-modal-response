<template>
    <div class="py-3 px-8">
        <div :class="sizeClass" v-text="block.value" />
    </div>
</template>

<script>
const SIZE_CLASSES = {
    small: 'text-sm font-semibold',
    medium: 'text-base font-semibold',
    large: 'text-lg font-bold',
}

export default {
    props: {
        block: { type: Object, required: true },
    },

    computed: {
        sizeClass() {
            return SIZE_CLASSES[this.block.size] ?? SIZE_CLASSES.medium
        },
    },
}
</script>
