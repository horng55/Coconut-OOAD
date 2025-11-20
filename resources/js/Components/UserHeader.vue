<script setup>
import {ref, computed} from "vue";
import {useGlobalStore} from "../Global/Global.js";
import {router, Link} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {useDarkMode} from "../Composables/darkMode.js";
import UserMenuItem from "./UserMenuItem.vue";
import ConfirmDialog from "../Components/ConfirmDialog.vue";
import AppHelper from "../Helper/AppHelper.js";

const {isDark, toggleDarkMode} = useDarkMode();
const store = useGlobalStore();
const confirmDialog = ref(null);
const isSidebarOpen = ref(false);
const page = usePage();

const props = defineProps({
    title: Array,
});

const menuList = computed(() => store.user_sidebars);
// Hide items that are now available inside the profile dropdown (Subscription, Setting)
const filteredMenu = computed(() => {
    try {
        const subHref = route('subscription.index');
        const setHref = route('setting');
        return (menuList.value || []).filter((item) => {
            const href = item?.href || '';
            return !(href.startsWith(subHref) || href.startsWith(setHref));
        });
    } catch (e) {
        return menuList.value || [];
    }
});

const logout = async () => {
    const confirmed = await confirmDialog.value.show("Are you sure you want to logout?");
    if (confirmed) {
        router.post(route('logout'));
    }
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const isItemActive = (item) => {
    if (!item) return false;
    const hrefActive = item.href && page.url.startsWith(item.href);
    const childActive = Array.isArray(item.child) && item.child.some((c) => c.href && page.url.startsWith(c.href));
    return !!(hrefActive || childActive);
};

// Profile dropdown
const showProfileMenu = ref(false);
const toggleProfileMenu = () => {
    showProfileMenu.value = !showProfileMenu.value;
};

// Back navigation
const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        try {
            router.visit(route('dashboard'));
        } catch (e) {
            window.location.href = '/';
        }
    }
};
</script>

<template>
    <header class="relative shadow-2xl sticky top-0 z-50">
        
        <!-- Top bar -->
        <div class="flex justify-between items-center p-4">
            <!-- Page title -->
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl font-khmer-Battambang bg-gradient-to-r from-[#F87B1B] via-pink-400 to-purple-400 bg-clip-text text-transparent font-bold leading-tight">
                        {{ AppHelper.getPageTitle(title) || "Dashboard" }}
                    </h1>
                    <p class="text-xs text-white/40">Welcome</p>
                </div>
            </div>

            <!-- Right controls -->
            <div class="flex items-center space-x-2 md:space-x-3">
                <!-- Mobile menu button (moved to bottom nav) -->
                <button class="hidden"></button>

                

                <!-- Desktop menu buttons near profile (hidden on mobile) -->
                <div class="hidden md:flex items-center gap-2 ml-2">
                    <Link
                        v-for="item in filteredMenu"
                        :key="item.title"
                        :href="item.href"
                        class="h-10 px-3 rounded-xl bg-white/5 hover:bg-white/10 text-white/80 hover:text-white transition flex items-center gap-2 border border-white/10 hover:border-white/20"
                        :class="{ 'text-[#F87B1B] bg-white/10': isItemActive(item) }"
                    >
                        <span class="text-sm" v-html="item.icon"></span>
                        <span class="font-khmer-Battambang text-sm">{{ item.title }}</span>
                    </Link>
                </div>

                <!-- Dark mode toggle -->
                <button 
                    @click="toggleDarkMode"
                    class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-500/30 text-white hover:text-purple-400 transition-all duration-300 flex items-center justify-center shadow-lg hover:scale-105 transform"
                >
                    <span v-if="isDark"><i class="fa-solid fa-moon"></i></span>
                    <span v-else><i class="fa-solid fa-sun"></i></span>
                </button>

                <!-- Profile dropdown (moved near logout) -->
                <div class="relative block">
                    <button @click="toggleProfileMenu" class="flex items-center gap-2 h-10 w-10 sm:w-auto px-0 sm:px-3 rounded-xl bg-white/5 hover:bg-white/10 text-white transition justify-center">
                        <i class="fa-solid fa-user text-[#F87B1B]"></i>
                        <span class="font-khmer-Battambang text-sm hidden sm:inline">{{ store.currentUser?.name || "Guest" }}</span>
                        <i class="fa-solid fa-chevron-down text-xs opacity-80 hidden sm:inline"></i>
                    </button>
                    <div v-if="showProfileMenu" class="absolute right-0 mt-2 w-56 bg-black/70 backdrop-blur-md border border-white/10 rounded-xl shadow-2xl overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-white/10">
                            <div class="text-white/90 font-khmer-Battambang text-sm truncate">{{ store.currentUser?.name || "Guest" }}</div>
                            <div class="text-white/50 text-xs">User Menu</div>
                        </div>
                        <div class="py-2">
                            <Link :href="route('subscription.index')" @click="showProfileMenu=false" class="flex items-center gap-2 px-4 py-2 text-white/80 hover:text-white hover:bg-white/10 transition">
                                <i class="fa-solid fa-crown text-yellow-400"></i>
                                <span class="font-khmer-Battambang text-sm">Subscription</span>
                            </Link>
                            <Link :href="route('setting')" @click="showProfileMenu=false" class="flex items-center gap-2 px-4 py-2 text-white/80 hover:text-white hover:bg-white/10 transition">
                                <i class="fa-solid fa-gear"></i>
                                <span class="font-khmer-Battambang text-sm">Setting</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <button 
                    @click="logout"
                    class="relative group h-10 w-10 sm:w-auto px-0 sm:px-4 bg-gradient-to-r from-red-500/90 to-pink-500/90 hover:from-red-500 hover:to-pink-500 text-white font-khmer-Battambang rounded-xl transition-all duration-300 shadow-lg hover:shadow-red-500/50 transform hover:scale-105 overflow-hidden border border-red-500/20 flex items-center justify-center"
                >
                    <span class="relative z-10 flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="hidden sm:inline">Logout</span>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-pink-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>

        <!-- Sidebar menu moved into profile dropdown, hide here -->
        <nav class="hidden"></nav>

        <ConfirmDialog ref="confirmDialog"/>
    </header>

    <!-- Bottom Navigator Bar (mobile only) -->
    <nav class="fixed bottom-0 left-0 right-0 md:hidden bg-black/80 backdrop-blur-md border-t border-white/10 z-50">
        <div class="flex items-center justify-around py-2">
            <Link
                v-for="item in filteredMenu"
                :key="item.title"
                :href="item.href"
                class="w-16 h-14 rounded-xl flex flex-col items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition px-1"
                :class="{ 'text-[#F87B1B] bg-white/10': isItemActive(item) }"
                :aria-label="item.title"
            >
                <span class="text-lg" v-html="item.icon"></span>
                <span class="text-[10px] leading-tight mt-1 truncate w-full text-center">{{ item.title }}</span>
            </Link>

            
        </div>
        
        </nav>
</template>

<style scoped>
nav {
    transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
}

/* Text gradient support */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Glassmorphism effect enhancement */
.backdrop-blur-xl {
    -webkit-backdrop-filter: blur(24px);
    backdrop-filter: blur(24px);
}

.backdrop-blur-md {
    -webkit-backdrop-filter: blur(12px);
    backdrop-filter: blur(12px);
}

/* Smooth animations */
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

/* Button hover effects */
button {
    position: relative;
}

button:active {
    transform: scale(0.95);
}
</style>
