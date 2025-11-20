<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {computed} from 'vue';

const props = defineProps({
    title: String,
    href: String,
    active: Boolean,
});

const page = usePage();
const isActive = computed(() => props.active || page.url.startsWith(props.href));
</script>

<template>
    <Link
        :href="href"
        class="flex items-center space-x-3 py-2 px-4 rounded-lg transition-all duration-300"
        :class="[
      isActive
        ? 'text-white font-medium bg-gradient-to-r from-[#FF9013]/20 to-pink-500/20 border border-[#FF9013]/30 shadow-lg'
        : 'text-white/60 hover:text-white hover:bg-white/5 border border-transparent hover:border-white/10'
    ]"
    >
        <i class="fa-solid fa-circle text-[6px]" :class="isActive ? 'text-[#FF9013]' : 'text-white/40'"></i>
        <span class="truncate font-khmer-Battambang">{{ title }}</span>
    </Link>
</template>

<style scoped>
.truncate {
    max-width: 180px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
