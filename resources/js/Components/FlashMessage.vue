<script setup>
import {ref, watch} from "vue";
import {usePage} from "@inertiajs/vue3";

const page = usePage();

const alert = ref(null);
const visible = ref(false);
let timer = null;

const duration = 5000;

watch(
    () => page.props.flash?.alert,
    (newAlert) => {
        alert.value = newAlert || null;
        visible.value = !!newAlert;

        if (newAlert) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                visible.value = false;
            }, duration);
        }
    },
    {immediate: true}
);

const close = () => {
    visible.value = false;
    clearTimeout(timer);
};
</script>

<template>
    <transition name="fade">
        <div
            v-if="visible && alert"
            :class="[
        'fixed top-5 right-5 p-3 rounded shadow-lg z-50 font-medium',
        alert.type === 'success' ? 'bg-green-600 text-white' : '',
        alert.type === 'danger' ? 'bg-red-600 text-white' : '',
        alert.type === 'warning' ? 'bg-yellow-400 text-gray-900' : '',
        alert.type === 'info' ? 'bg-blue-600 text-white' : '',
      ]"
            role="alert"
        >
            <div class="flex items-center justify-between gap-4">
                <span class="font-khmer-Battambang">{{ alert.message }}</span>
                <button @click="close" class="font-bold hover:opacity-75">✖</button>
            </div>
        </div>
    </transition>
</template>

<style scoped>
</style>
