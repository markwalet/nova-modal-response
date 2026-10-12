<template>
    <div class="nmr-code">
        <CopyButton :value="block.value" class="nmr-code__copy" />
        <template v-if="block.highlight === false">
            <pre><code ref="plaintextCode" class="language-plaintext" v-text="block.value" /></pre>
        </template>
        <!-- autodetect defaults to true in the plugin and would override `language`. -->
        <highlightjs v-else language="json" :autodetect="false" :code="block.value" />
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
