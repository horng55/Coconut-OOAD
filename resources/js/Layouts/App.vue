<script setup>
import SideBar from "../Components/SideBar.vue";
import Header from "../Components/Header.vue";
import {useDarkMode} from "../Composables/darkMode.js";

const {isDark} = useDarkMode();

defineProps({
    title: Array,
});
</script>

<template>
    <div :class="{ 'dark': isDark }">
        <div class="flex h-screen bg-white dark:bg-gradient-to-br dark:from-gray-900 dark:via-slate-900 dark:to-gray-900 transition-all duration-300">
            <!-- Sidebar -->
            <SideBar class="w-64 shrink-0 transition-all duration-300"/>

            <div class="flex-1 flex flex-col transition-all duration-300">
                <Header :title="title"/>
                <div class="p-6 flex-1 overflow-auto">
                    <slot/>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar */
:deep(*) {
    scrollbar-width: thin;
    scrollbar-color: rgba(37, 99, 235, 0.5) rgba(0, 0, 0, 0.1);
}

:deep(*::-webkit-scrollbar) {
    width: 8px;
    height: 8px;
}

:deep(*::-webkit-scrollbar-track) {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
}

:deep(*::-webkit-scrollbar-thumb) {
    background: linear-gradient(to bottom, rgba(37, 99, 235, 0.6), rgba(5, 150, 105, 0.6));
    border-radius: 4px;
}

:deep(*::-webkit-scrollbar-thumb:hover) {
    background: linear-gradient(to bottom, rgba(37, 99, 235, 0.8), rgba(5, 150, 105, 0.8));
}
</style>
