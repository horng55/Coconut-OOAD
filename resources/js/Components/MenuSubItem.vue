<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {computed} from 'vue';

const props = defineProps({
    title: String,
    href: String,
    active: Boolean,
});

const page = usePage();
const isActive = computed(() => props.active || (props.href && page.url.startsWith(props.href)));
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        class="flex items-center gap-3 py-2.5 px-4 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 group"
        :class="{ 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 font-medium border-l-2 border-blue-500': isActive }">
        <i class="fa-solid fa-circle text-[6px] transition-all duration-300" 
           :class="isActive ? 'text-blue-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400'"></i>
        <span class="truncate text-sm">{{ title }}</span>
    </Link>
    <!-- Disabled Submenu Item (no href) -->
    <div
        v-else
        class="flex items-center gap-3 py-2.5 px-4 rounded-lg text-gray-400 dark:text-gray-500 cursor-not-allowed opacity-60">
        <i class="fa-solid fa-circle text-[6px] text-gray-300 dark:text-gray-600"></i>
        <span class="truncate text-sm">{{ title }}</span>
    </div>
</template>

<style scoped>
.truncate {
    max-width: 180px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
