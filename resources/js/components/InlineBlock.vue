<template>
    <div class="py-2 px-8">
        <div
            class="flex flex-wrap items-center gap-2"
            :class="alignmentClass"
        >
            <component
                v-for="(atom, index) in (block.value ?? [])"
                :is="blockComponents[atom.type]"
                :key="index"
                :block="atom"
                :inline="true"
            />
        </div>
    </div>
</template>

<script>
import { blockComponents } from './blockComponents.js'

const ALIGNMENT_CLASSES = {
    default: 'justify-start',
    spread: 'justify-between',
    center: 'justify-center',
    end: 'justify-end',
}

export default {
    props: {
        block: { type: Object, required: true },
    },

    data() {
        return {
            blockComponents,
        }
    },

    computed: {
        alignmentClass() {
            return ALIGNMENT_CLASSES[this.block.alignment] ?? ALIGNMENT_CLASSES.default
        },
    },
}
</script>
