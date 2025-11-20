<script setup>
import {router} from "@inertiajs/vue3";

defineProps({
    links: Array,
});

const navigate = (url) => {
    if (!url) return;
    router.get(url, {}, {preserveScroll: true});
};
</script>

<template>
    <nav v-if="links.length > 3" class="flex justify-center mt-6 space-x-2">
        <template v-for="(link, index) in links" :key="index">
            <span
                v-if="!link.url"
                class="px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-400 cursor-not-allowed"
                v-html="link.label"
            ></span>

            <button
                v-else
                @click.prevent="navigate(link.url)"
                class="px-3 py-2 rounded-lg transition duration-200"
                :class="link.active ? 'bg-orange-500 text-white font-bold' : 'bg-gray-100 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'"
                v-html="link.label"
            ></button>
        </template>
    </nav>
</template>
