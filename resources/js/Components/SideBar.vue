<script setup>
import MenuItem from "./MenuItem.vue";
import {ref, computed, watch} from "vue";
import {Link} from "@inertiajs/vue3";
import {useGlobalStore} from "../Global/Global.js";

const globalStore = useGlobalStore();
const menuList = computed(() => globalStore.admin_sidebars);
const search = ref("");
const searchMenu = ref([]);
const previousActiveMenus = ref([]);
const isSidebarOpen = ref(false);

const sidebars = computed(() => (search.value.trim() ? searchMenu.value : menuList.value));

watch(search, (newValue) => {
    filterMenu(newValue.trim());
});

function filterMenu(query) {
    if (!query) {
        searchMenu.value = [];
        return;
    }

    searchMenu.value = menuList.value
        .map(item => {
            const parentMatch = item.title.toLowerCase().includes(query.toLowerCase());
            const children = item.child?.filter(child =>
                child.title.toLowerCase().includes(query.toLowerCase())
            ) || [];

            if (parentMatch || children.length > 0) {
                return {
                    ...item,
                    child: children,
                    active: previousActiveMenus.value.includes(item.title),
                };
            }
            return null;
        })
        .filter(item => item !== null);
}

watch(sidebars, (newVal) => {
    previousActiveMenus.value = newVal.filter(item => item.active).map(item => item.title);
});

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
    <!-- Mobile Toggle Button -->
    <button
        @click="toggleSidebar"
        class="fixed top-4 left-4 w-12 h-12 flex items-center justify-center bg-gradient-to-r from-blue-600 to-emerald-600 text-white rounded-xl shadow-2xl md:hidden z-50 transition-all duration-300 hover:scale-110 hover:shadow-blue-500/50">
        <i :class="isSidebarOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars'" class="text-xl"></i>
    </button>

    <div
        v-bind="$attrs"
        :class="[
            'fixed md:relative top-0 left-0 w-64 h-screen flex flex-col transform transition-all duration-300 z-40',
            'bg-white dark:bg-gray-800',
            'border-r border-gray-200 dark:border-gray-700 shadow-xl',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
            'md:translate-x-0'
        ]">

        <!-- Logo Section -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <Link :href="route('dashboard.index')" class="flex items-center gap-3 group">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-graduation-cap text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-emerald-600 bg-clip-text text-transparent">
                        School Management
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Admin Portal</p>
                </div>
            </Link>
        </div>

        <!-- Search Bar -->
        <div class="p-4 bg-white dark:bg-gray-800">
            <div class="relative">
                <input
                    type="search"
                    placeholder="Search menus..."
                    v-model="search"
                    class="w-full px-4 py-3 pl-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-300"
                />
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-400"></i>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="flex-1 overflow-y-auto px-3 pb-4 custom-scrollbar min-h-0 bg-white dark:bg-gray-800">
            <div v-if="search && searchMenu.length === 0" class="flex flex-col items-center justify-center py-8 px-4">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-search text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center">No results found</p>
            </div>

            <MenuItem
                v-for="item in sidebars"
                :key="item.title"
                :title="item.title"
                :href="item.href"
                :active="item.active"
                :item="item?.child">
                <template v-slot:icon>
                    <span v-html="item.icon"></span>
                </template>
            </MenuItem>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0 bg-white dark:bg-gray-800">
            <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-600 dark:text-gray-400">System Active</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(59, 130, 246, 0.5) rgba(0, 0, 0, 0.1);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.5);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.7);
}

/* Text gradient support */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
