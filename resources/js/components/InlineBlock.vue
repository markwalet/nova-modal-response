<template>
    <div class="py-3 px-8">
        <div
            class="flex flex-wrap items-center gap-x-2 gap-y-1"
            :class="block.spread ? 'justify-between' : 'justify-start'"
        >
            <component
                v-for="(atom, index) in (block.value ?? [])"
                :is="blockComponents[atom.type]"
                :key="index"
                :block="atom"
            />
        </div>
    </div>
</template>

<script>
import { blockComponents } from './blockComponents.js'

export default {
    props: {
        block: { type: Object, required: true },
    },

    data() {
        return {
            blockComponents,
        }
    },
}
</script>
