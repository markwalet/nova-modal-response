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
import DividerBlock from './DividerBlock.vue'
import HtmlBlock from './HtmlBlock.vue'
import HeadingBlock from './HeadingBlock.vue'
import ListBlock from './ListBlock.vue'
import BadgeBlock from './BadgeBlock.vue'
import CodeBlock from './CodeBlock.vue'
import JsonBlock from './JsonBlock.vue'

// v1 wire keys that v2 dropped in favour of `data.blocks`. A payload still
// carrying any of these renders an empty modal body, so we warn instead of
// silently rendering nothing. See ADR-0001 and UPGRADE.md.
const LEGACY_KEYS = ['body', 'code', 'html', 'highlight']

// Tracks `data` objects already warned about so re-renders don't spam the
// console. A fresh modal open ships a fresh `data` object, so it still warns.
const warnedPayloads = new WeakSet()

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
                divider: DividerBlock,
                html: HtmlBlock,
                heading: HeadingBlock,
                list: ListBlock,
                badge: BadgeBlock,
                code: CodeBlock,
                json: JsonBlock,
            },
        }
    },

    mounted() {
        this.warnOnLegacyPayload()
    },

    methods: {
        handleClose() {
            this.$emit('close')
        },

        warnOnLegacyPayload() {
            if (warnedPayloads.has(this.data)) {
                return
            }

            const hasLegacyKeys = LEGACY_KEYS.some(
                key => Object.prototype.hasOwnProperty.call(this.data, key)
            )

            if (!hasLegacyKeys) {
                return
            }

            warnedPayloads.add(this.data)

            console.warn(
                '[nova-modal-response] Legacy payload keys detected on data (one of: body, code, html, highlight).\n' +
                'These were removed in v2.0. Use ModalResponse::stack(...) or the static sugar\n' +
                '(ModalResponse::text/code/html/json) on the PHP side. See UPGRADE.md.'
            )
        },
    },
}
</script>
