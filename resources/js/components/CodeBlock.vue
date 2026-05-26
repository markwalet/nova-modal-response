<template>
    <template v-if="block.highlight === false">
        <pre><code ref="plaintextCode" class="language-plaintext" v-text="block.value" /></pre>
    </template>
    <highlightjs v-else-if="block.language" :language="block.language" :code="block.value" />
    <highlightjs v-else autodetect :code="block.value" />
</template>

<script>
import hljs from 'highlight.js/lib/common'
import hljsVuePlugin from '@highlightjs/vue-plugin'

export default {
    components: {
        highlightjs: hljsVuePlugin.component,
    },

    props: {
        block: { type: Object, required: true },
    },

    mounted() {
        if (this.block.highlight === false && this.$refs.plaintextCode) {
            hljs.highlightElement(this.$refs.plaintextCode)
        }
    },
}
</script>
