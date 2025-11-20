<script setup>
import {useDarkMode} from "../Composables/darkMode.js";
import {router, usePage} from "@inertiajs/vue3";
import {computed, ref, onMounted, onUnmounted} from "vue";

const {isDark, toggleDarkMode} = useDarkMode();
const page = usePage();
const showProfileMenu = ref(false);

defineProps({
    title: Array,
});

const currentParent = computed(() => {
    try {
        const parentData = page.props.current_parent;
        if (!parentData) return null;
        if (typeof parentData === 'string') {
            return JSON.parse(parentData);
        }
        return parentData;
    } catch {
        return null;
    }
});

const logout = () => {
    if (confirm('Are you sure you want to logout?')) {
        router.post(route('parent.logout'));
    }
};

const toggleProfileMenu = () => {
    showProfileMenu.value = !showProfileMenu.value;
};

// Close profile menu when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.profile-menu-container')) {
        showProfileMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div :class="{ 'dark': isDark }">
        <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900 transition-all duration-300">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-md border-b border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-indigo-500"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                            Parent Portal
                        </h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Dark Mode Toggle -->
                        <button 
                            @click="toggleDarkMode"
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-all duration-200"
                            title="Toggle Dark Mode"
                        >
                            <i v-if="isDark" class="fas fa-moon text-sm"></i>
                            <i v-else class="fas fa-sun text-sm"></i>
                        </button>

                        <!-- Profile Menu -->
                        <div class="relative profile-menu-container">
                            <button
                                @click="toggleProfileMenu"
                                class="h-10 flex items-center gap-2 px-3 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200"
                            >
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-user text-white text-xs"></i>
                                </div>
                                <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-300">{{ currentParent?.user?.full_name || "Parent" }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400"></i>
                            </button>

                            <!-- Profile Dropdown Menu -->
                            <div 
                                v-if="showProfileMenu"
                                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
                            >
                                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ currentParent?.user?.full_name || "Parent" }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ currentParent?.user?.email || "" }}</p>
                                </div>
                                <div class="p-2">
                                    <button
                                        @click="logout"
                                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="p-6">
                <slot/>
            </div>
        </div>
    </div>
</template>

