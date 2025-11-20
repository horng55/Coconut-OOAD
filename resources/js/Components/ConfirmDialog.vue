<script setup>
import {ref} from "vue";

const isOpen = ref(false);
const resolveAction = ref(null);
const message = ref("Are you sure?");

const show = (msg) => {
    message.value = msg;
    isOpen.value = true;

    return new Promise((resolve) => {
        resolveAction.value = resolve;
    });
};

const confirm = () => {
    resolveAction.value(true);
    isOpen.value = false;
};

const cancel = () => {
    resolveAction.value(false);
    isOpen.value = false;
};

defineExpose({show});
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed top-0 left-0 right-0 bottom-0 flex items-center justify-center bg-black bg-opacity-50 p-4" style="z-index: 9999;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-6 w-full max-w-md">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Confirmation</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ message }}</p>

                <div class="mt-4 flex justify-end space-x-3">
                    <button
                        @click="cancel"
                        class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition">
                        Cancel
                    </button>
                    <button
                        @click="confirm"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        Ok
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-active .bg-white,
.fade-enter-active .dark\:bg-gray-800 {
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
