<template>
    <div class="py-3 px-8">
        <p v-text="block.value"/>
    </div>
</template>

<script>
export default {
    props: {
        block: { type: Object, required: true },
    },
}
</script>
