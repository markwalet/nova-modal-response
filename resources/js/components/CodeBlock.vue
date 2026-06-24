<template>
    <div class="nmr-code">
        <CopyButton :value="block.value" class="nmr-code__copy" />
        <template v-if="block.highlight === false">
            <pre><code ref="plaintextCode" class="language-plaintext" v-text="block.value" /></pre>
        </template>
        <highlightjs v-else-if="block.language" :language="block.language" :code="block.value" />
        <highlightjs v-else autodetect :code="block.value" />
    </div>
</template>

<script>
import hljs from 'highlight.js/lib/common'
import hljsVuePlugin from '@highlightjs/vue-plugin'
import CopyButton from './CopyButton.vue'

export default {
    components: {
        CopyButton,
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
