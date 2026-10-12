<template>
    <div class="py-3 px-8">
        <component :is="block.ordered ? 'ol' : 'ul'" class="nmr-list">
            <li v-for="(item, index) in block.value" :key="index" v-text="item" />
        </component>
    </div>
</template>

<script>
export default {
    props: {
        block: { type: Object, required: true },
    },
}
</script>
