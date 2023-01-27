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
                <pre v-if="data.code" class="block whitespace-pre overflow-x-scroll bg-slate-800 text-slate-50 rounded-lg p-5 shadow-md">
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
</style>
