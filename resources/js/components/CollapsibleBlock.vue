<template>
    <div class="py-3 px-8">
        <button
            type="button"
            class="flex w-full items-center gap-x-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200"
            :aria-expanded="expanded"
            @click.prevent="toggle"
        >
            <Icon
                :name="expanded ? 'chevron-down' : 'chevron-right'"
                class="w-4 h-4 text-gray-500 dark:text-gray-400"
            />
            <span v-text="block.header" />
        </button>

        <div v-show="expanded" class="mt-2 -mx-8">
            <component
                v-for="(child, index) in (block.value ?? [])"
                :is="blockComponents[child.type]"
                :key="index"
                :block="child"
            />
        </div>
    </div>
</template>

<script>
import { Icon } from 'laravel-nova-ui'
import { blockComponents } from './blockComponents.js'

export default {
    components: { Icon },

    props: {
        block: { type: Object, required: true },
    },

    data() {
        return {
            blockComponents,
            expanded: this.block.expanded ?? false,
        }
    },

    methods: {
        toggle() {
            this.expanded = !this.expanded
        },
    },
}
</script>
