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
                <code>{{ data.result }}</code>
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
