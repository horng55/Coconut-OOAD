<script setup>
import {ref, computed, watch, onMounted} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import MenuSubItem from "./MenuSubItem.vue";

const props = defineProps({
    title: String,
    href: String,
    item: Array,
    active: Boolean,
});

const page = usePage();
const expanded = ref(false);

const isSubmenuActive = computed(() =>
    props.item?.some(menu => menu.href && page.url.startsWith(menu.href))
);

// Check if parent menu item is active based on current route
const isParentActive = computed(() => {
    if (props.active) return true;
    if (props.href && page.url.startsWith(props.href)) return true;
    return isSubmenuActive.value;
});

// Check if single menu item is active
const isSingleItemActive = computed(() => {
    if (props.active) return true;
    if (props.href && page.url.startsWith(props.href)) return true;
    return false;
});

// Computed expanded state - always expanded if submenu is active
const isExpanded = computed(() => expanded.value || isSubmenuActive.value);

// Auto-expand if any submenu item is active - watch both isSubmenuActive and page.url
watch([isSubmenuActive, () => page.url], ([isActive]) => {
    if (isActive) {
        expanded.value = true;
    }
}, { immediate: true, deep: true });

// Initialize expanded state on mount
onMounted(() => {
    if (isSubmenuActive.value) {
        expanded.value = true;
    }
});

const toggle = () => {
    // Don't allow collapsing if a submenu is active
    if (isSubmenuActive.value) {
        return; // Keep expanded if submenu is active
    }
    expanded.value = !expanded.value;
};
</script>

<template>
    <div v-bind="$attrs" class="menu-item mb-1">
        <!-- Parent Item with Submenu -->
        <div v-if="item?.length">
            <button
                class="flex justify-between items-center w-full py-3 px-4 text-left text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 group"
                :class="{ 'bg-gradient-to-r from-blue-500 to-emerald-500 text-white shadow-lg': isParentActive }"
                @click="toggle">
                <span class="flex items-center gap-3">
                    <span :class="[
                        'w-5 text-center transition-transform',
                        isParentActive ? 'text-white group-hover:scale-110' : 'text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:scale-110'
                    ]">
                        <slot name="icon"/>
                    </span>
                    <span class="font-medium">{{ title }}</span>
                </span>
                <i :class="[
                    'fa-solid fa-chevron-down transition-transform duration-300',
                    isParentActive ? 'text-white/80' : 'text-gray-500 dark:text-gray-400',
                    { 'rotate-180': isExpanded }
                ]"/>
            </button>

            <!-- Submenu Items -->
            <transition name="slide">
                <div v-show="isExpanded" class="pl-6 mt-1 space-y-1">
                    <MenuSubItem
                        v-for="menu in item"
                        :key="menu.title"
                        :title="menu.title"
                        :href="menu.href"
                        :active="menu.active || (menu.href && page.url.startsWith(menu.href))"
                    />
                </div>
            </transition>
        </div>

        <!-- Single Menu Item -->
        <Link
            v-else-if="href"
            :href="href"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 group"
            :class="{ 'bg-gradient-to-r from-blue-500 to-emerald-500 text-white shadow-lg': isSingleItemActive }">
            <span :class="[
                'w-5 text-center transition-transform',
                isSingleItemActive ? 'text-white group-hover:scale-110' : 'text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:scale-110'
            ]">
                <slot name="icon"/>
            </span>
            <span class="font-medium">{{ title }}</span>
        </Link>
        <!-- Disabled Menu Item (no href) -->
        <div
            v-else
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 dark:text-gray-500 cursor-not-allowed opacity-60">
            <span class="w-5 text-center">
                <slot name="icon"/>
            </span>
            <span class="font-medium">{{ title }}</span>
        </div>
    </div>
</template>

<style scoped>
/* Rotate icon for expanded submenu */
.rotate-180 {
    transform: rotate(180deg);
}

/* Slide transition for submenu */
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease;
    max-height: 500px;
    overflow: hidden;
}

.slide-enter-from,
.slide-leave-to {
    max-height: 0;
    opacity: 0;
    transform: translateY(-10px);
}

.slide-enter-to,
.slide-leave-from {
    max-height: 500px;
    opacity: 1;
    transform: translateY(0);
}
</style>
