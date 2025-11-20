<script setup>
import {ref, watch} from "vue";

const props = defineProps({
    type: {type: String, default: "info"},
    message: String,
    show: Boolean,
    autoClose: {type: Boolean, default: true},
    duration: {type: Number, default: 3000},
});

const isVisible = ref(props.show);
let timer = null;

watch(() => props.show, (newVal) => {
    isVisible.value = newVal;

    if (newVal && props.autoClose) {
        clearTimeout(timer);
        timer = setTimeout(() => {
            isVisible.value = false;
        }, props.duration);
    }
});

const closeAlert = () => {
    isVisible.value = false;
    clearTimeout(timer);
};

const alertClasses = {
    success: "bg-green-500 text-white",
    error: "bg-red-500 text-white",
    warning: "bg-yellow-500 text-gray-800",
    info: "bg-blue-500 text-white",
};
</script>

<template>
    <transition name="fade">
        <div
            v-if="isVisible"
            :class="['fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg z-50', alertClasses[type], 'dark:bg-gray-800 dark:text-gray-200']"
            role="alert"
        >
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium">{{ message }}</span>
                <button @click="closeAlert" class="ml-4 text-white dark:text-gray-400 hover:opacity-80">
                    ✖
                </button>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
