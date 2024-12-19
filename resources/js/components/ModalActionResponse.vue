<template>
    <Modal :show="show"
           :size="data.size ?? '2xl'"
           @close-via-escape="handleClose"
           role="alertdialog">
        <div class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <slot>
                <ModalHeader v-text="data.title" />

                <template v-if="data.code">
                    <highlightjs v-if="data.highlight" autodetect :code="data.code" />
                    <pre class="manual-code-block" v-else>
                        <code v-text="data.code"></code>
                    </pre>
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
import 'highlight.js/lib/common';
import hljsVuePlugin from "@highlightjs/vue-plugin";
import { Button } from 'laravel-nova-ui'

export default {
    components: {
        Button,
        highlightjs: hljsVuePlugin.component
    },

    emits: ['confirm', 'close'],

    props: {
        show: { type: Boolean, default: false },
        data: { type: Object, required: true }
    },

    methods: {
        handleClose() {
            this.$emit('close')
        },
    },
}
</script>

<style scoped>
pre.manual-code-block {
    color: #f8fafc;
    background-color: #1e293b;
    overflow-x: auto;
    padding: 1.25rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -2px rgb(0 0 0 / 10%);
    display: flex;
}
</style>
