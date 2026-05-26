<template>
    <Modal :show="show"
           :size="data.size ?? '2xl'"
           @close-via-escape="handleClose"
           role="alertdialog">
        <div class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <slot>
                <ModalHeader v-text="data.title" />

                <component
                    v-for="(block, index) in (data.blocks ?? [])"
                    :is="blockComponents[block.type]"
                    :key="index"
                    :block="block"
                />
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
import { Button } from 'laravel-nova-ui'
import TextBlock from './TextBlock.vue'

export default {
    components: {
        Button,
    },

    emits: ['confirm', 'close'],

    props: {
        show: { type: Boolean, default: false },
        data: { type: Object, required: true },
    },

    data() {
        return {
            blockComponents: {
                text: TextBlock,
            },
        }
    },

    methods: {
        handleClose() {
            this.$emit('close')
        },
    },
}
</script>
