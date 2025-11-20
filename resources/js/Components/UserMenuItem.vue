<script setup>
import {ref, computed} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import UserMenuSubItem from "./UserMenuSubItem.vue";

const props = defineProps({
    title: String,
    href: String,
    item: Array,
    active: Boolean,
});

const page = usePage();
const expanded = ref(false);

const isSubmenuActive = computed(() =>
    props.item?.some(menu => page.url.startsWith(menu.href))
);

// Also treat the direct link as active when current URL starts with href
const isActive = computed(() => props.active || (!!props.href && page.url.startsWith(props.href)));

if (isSubmenuActive.value) {
    expanded.value = true;
}

const toggle = () => {
    expanded.value = !expanded.value;
};
</script>

<template>
    <div v-bind="$attrs" class="menu-item text-sm my-2 md:my-1 relative">
        <!-- Expandable menu -->
        <div v-if="item?.length">
            <button
                @click="toggle"
                class="relative flex justify-between items-center w-full py-3 px-4 text-left text-white/80 hover:text-white bg-white/5 hover:bg-white/10 backdrop-blur-md border border-white/10 hover:border-white/20 transition-all duration-300 rounded-xl shadow-lg"
                :class="{ 'ring-1 ring-[#F87B1B]/40 text-white': expanded || isSubmenuActive }"
                :aria-expanded="(expanded || isSubmenuActive) ? 'true' : 'false'"
                :aria-current="(expanded || isSubmenuActive) ? 'page' : undefined">
                <!-- Active indicator -->
                <span v-if="expanded || isSubmenuActive" class="absolute left-1 top-1/2 -translate-y-1/2 h-5 w-1 bg-[#F87B1B] rounded-full"></span>
                <span class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-[#F87B1B]/20 to-pink-500/20 rounded-lg flex items-center justify-center">
                        <slot name="icon"/>
                    </div>
                    <span class="font-khmer-Battambang" :class="{ 'font-semibold text-white': expanded || isSubmenuActive }">{{ title }}</span>
                </span>
                <i
                    class="fa-solid fa-angle-down transition-transform duration-200 text-white/60"
                    :class="{ 'rotate-180': expanded || isSubmenuActive }"
                />
            </button>

            <!-- Submenu -->
            <div
                v-if="expanded || isSubmenuActive"
                class="pl-6 mt-2 space-y-1 transition-all duration-300 ease-in-out">
                <UserMenuSubItem
                    v-for="menu in item"
                    :key="menu.title"
                    :title="menu.title"
                    :href="menu.href"
                    :active="page.url.startsWith(menu.href)"
                    class="block text-white/60 hover:text-white transition"
                />
            </div>
        </div>

        <!-- Regular link -->
        <Link
            v-else
            :href="href"
            class="relative flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 bg-white/5 hover:bg-white/10 backdrop-blur-md border border-white/10 hover:border-white/20 text-white/80 hover:text-white shadow-lg"
            :class="{ 'active': isActive }"
            :aria-current="isActive ? 'page' : undefined"
        >
            <!-- Active indicator -->
            <span v-if="isActive" class="absolute left-1 top-1/2 -translate-y-1/2 h-5 w-1 bg-[#F87B1B] rounded-full"></span>
            <div class="w-8 h-8 bg-gradient-to-br from-[#F87B1B]/20 to-pink-500/20 rounded-lg flex items-center justify-center">
                <slot name="icon"/>
            </div>
            <span class="font-khmer-Battambang" :class="{ 'font-semibold text-white': isActive }">{{ title }}</span>
        </Link>
    </div>
</template>

<style scoped>
.active {
    background: linear-gradient(135deg, rgba(248, 123, 27, 0.20), rgba(236, 72, 153, 0.20));
    border-color: rgba(248, 123, 27, 0.50) !important;
    color: white !important;
    box-shadow: 0 8px 16px rgba(248, 123, 27, 0.30);
}

.rotate-180 {
    transform: rotate(180deg);
}

.menu-item {
    border-radius: 0.75rem;
}

/* Glassmorphism effect enhancement */
.backdrop-blur-md {
    -webkit-backdrop-filter: blur(12px);
    backdrop-filter: blur(12px);
}
</style>


