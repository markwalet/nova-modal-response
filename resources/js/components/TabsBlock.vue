<template>
    <div class="tab-group py-3 px-8">
        <div class="tab-card">
            <div
                class="tab-menu divide-x dark:divide-gray-700 border-l-gray-200 border-r-gray-200 border-t-gray-200 border-b-gray-200 dark:border-l-gray-700 dark:border-r-gray-700 dark:border-t-gray-700 dark:border-b-gray-700"
                role="tablist"
            >
                <button
                    v-for="(tab, index) in tabs"
                    :key="index"
                    type="button"
                    class="tab-item"
                    :class="[
                        index === activeIndex
                            ? 'active text-primary-500 font-bold border-b-2 !border-b-primary-500'
                            : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 hover:dark:text-gray-200',
                    ]"
                    role="tab"
                    :aria-selected="index === activeIndex"
                    @click.prevent="activeIndex = index"
                >
                    <span class="capitalize">{{ tab.label }}</span>
                </button>
            </div>

            <div v-if="activeTab" class="mt-2 -mx-8">
                <component
                    v-for="(child, childIndex) in (activeTab.value ?? [])"
                    :is="blockComponents[child.type]"
                    :key="childIndex"
                    :block="child"
                />
            </div>
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
