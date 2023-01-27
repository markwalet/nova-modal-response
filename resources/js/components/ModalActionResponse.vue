<template>
    <Modal @modal-close="handleClose"
           tabindex="-1"
           role="dialog"
           size="2xl">
        <div
            @submit.prevent="handleConfirm"
            slot-scope="props"
            class="bg-white rounded-lg shadow-lg overflow-hidden dark:bg-gray-800 space-y-6"
        >
            <ModalHeader v-text="data.title"/>
            <ModalContent>
                <p v-if="data.html" v-html="data.html" />
                <div v-if="data.body" v-text="data.body" />
                <pre v-if="data.code">
                    <code v-text="data.code"></code>
                </pre>
            </ModalContent>
            <ModalFooter>
                <div class="flex items-center ml-auto">
                    <CancelButton
                        component="button"
                        type="button"
                        dusk="cancel-action-button"
                        class="ml-auto mr-3"
                        @click="$emit('close')"
                    >
                        Cancel
                    </CancelButton>

                </div>
            </ModalFooter>
        </div>

    </Modal>
</template>

<script>
export default {
    name: "ModalActionResponse",

    props: ["data"],
    methods: {
        handleClose() {
            this.$emit('close')
        },
        handleConfirm() {
            this.$emit('confirm')
        },
    },
}
</script>

<style scoped>
pre {
    color: #f8fafc;
    background-color: #1e293b;
    overflow-x: auto;
    border-radius: 0.75rem;
    padding: 1.25rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -2px rgb(0 0 0 / 10%);
    display: flex;
}
</style>
