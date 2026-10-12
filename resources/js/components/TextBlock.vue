<template>
    <div :class="inline ? '' : 'py-2 px-8'">
        <p v-text="block.value"/>
    </div>
</template>

<script>
export default {
    props: {
        block: { type: Object, required: true },
        inline: { type: Boolean, default: false },
    },
}
</script>
