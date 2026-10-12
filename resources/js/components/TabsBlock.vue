<template>
    <div>
        <div
            class="flex border-b border-gray-200 dark:border-gray-700"
            role="tablist"
        >
            <button
                v-for="(tab, index) in tabs"
                :key="index"
                type="button"
                class="px-6 py-3 text-sm focus:outline-none border-b-2"
                :class="[
                    index === activeIndex
                        ? 'text-primary-500 font-bold border-primary-500'
                        : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 hover:dark:text-gray-200 border-transparent font-semibold',
                ]"
                role="tab"
                :aria-selected="index === activeIndex"
                @click.prevent="activeIndex = index"
            >
                <span class="capitalize">{{ tab.label }}</span>
            </button>
        </div>

        <div v-if="activeTab">
            <component
                v-for="(child, childIndex) in (activeTab.value ?? [])"
                :is="blockComponents[child.type]"
                :key="childIndex"
                :block="child"
            />
        </div>
    </div>
</template>

<script>
import { blockComponents } from './blockComponents.js'

function resolveInitialIndex(tabs) {
    let index = 0

    tabs.forEach((tab, i) => {
        if (tab.active === true) {
            index = i
        }
    })

    return index
}

export default {
    props: {
        block: { type: Object, required: true },
    },

    data() {
        const tabs = this.block.value ?? []

        return {
            blockComponents,
            tabs,
            activeIndex: resolveInitialIndex(tabs),
        }
    },

    computed: {
        activeTab() {
            return this.tabs[this.activeIndex] ?? null
        },
    },
}
</script>
