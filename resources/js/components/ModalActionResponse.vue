<template>
    <Modal :show="show"
           :size="data.size ?? '2xl'"
           @close-via-escape="handleClose"
           role="alertdialog">
        <div class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <slot>
                <ModalHeader v-text="data.title" />

                <template v-if="data.code">
                    <pre v-if="data.highlight === false"><code v-text="data.code" class="language-plaintext" ref="plaintextCode"></code></pre>
                    <highlightjs v-else autodetect :code="data.code" />
                </template>
                <div v-else class="py-3 px-8">
                    <div v-if="data.html" v-html="data.html"/>
                    <p v-if="data.body" v-text="data.body"/>
                </div>
            </slot>

            <ModalFooter>
                <div class="ml-auto">
                    <Button
                        variant="link"
                        state="mellow"
                        @click.prevent="handleClose"
                        class="mr-3"
                        dusk="close-modal-button"
                    >
                        {{ data.closeButtonText ?? 'Close' }}
                    </Button>
                </div>
            </ModalFooter>
        </div>
    </Modal>
</template>

<script>
import hljs from 'highlight.js/lib/common';
import hljsVuePlugin from "@highlightjs/vue-plugin";
import { Button } from 'laravel-nova-ui'

export default {
    components: {
        Button,
        highlightjs: hljsVuePlugin.component
    },

    emits: ['confirm', 'close'],

    mounted() {
        if (this.data.code && this.data.highlight === false) {
            hljs.highlightElement(this.$refs.plaintextCode);
        }
    },

    props: {
        show: { type: Boolean, default: false },
        data: { type: Object, required: true },
    },

    methods: {
        handleClose() {
            this.$emit('close')
        },
    },
}
</script>
