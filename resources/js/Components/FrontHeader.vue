<template>
    <div class="w-full bg-white/5 backdrop-blur-2xl py-4 px-6 shadow-2xl fixed top-0 left-0 z-50 border-b border-white/10">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            <!-- Brand Name -->
            <Link :href="route('home.index')" class="flex items-center group">
                <h1 class="text-white text-xl md:text-2xl font-khmer-Moul group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-[#F87B1B] group-hover:to-pink-400 group-hover:bg-clip-text transition-all duration-300">
                    ចុងភៅ ឈិន តាំង
                </h1>
            </Link>

            <!-- Hamburger Menu for Mobile -->
            <button @click="toggleMenu" class="md:hidden text-white text-2xl focus:outline-none hover:text-orange-400 transition">
                <i :class="showMenu ? 'fas fa-times' : 'fas fa-bars'"></i>
            </button>

            <!-- Desktop Navigation -->
            <nav :class="[
                'md:flex md:space-x-1 md:items-center',
                showMenu ? 'block' : 'hidden',
                'absolute md:relative top-full left-0 w-full md:w-auto bg-black/80 md:bg-transparent backdrop-blur-3xl md:backdrop-blur-none shadow-2xl md:shadow-none p-4 md:p-0 transform transition-all duration-300 ease-in-out origin-top border-t md:border-0 border-white/10'
            ]">
                <Link :href="route('home.index')"
                      class="block md:inline text-sm font-khmer-Battambang text-white/80 hover:text-white hover:bg-white/10 py-3 px-4 rounded-xl text-center transition-all">
                    ទំព័រដើម
                </Link>
                <a href="https://www.appsheet.com/start/fd8f3a27-d4db-4f3f-886d-d2965e86654d" 
                   target="_blank"
                   class="block md:inline text-sm font-khmer-Battambang text-white/80 hover:text-white hover:bg-white/10 py-3 px-4 rounded-xl text-center transition-all">
                    កក់ ឬជួលឥវ៉ាន់
                </a>
                <Link :href="route('function.index')"
                      class="block md:inline text-sm font-khmer-Battambang text-white/80 hover:text-white hover:bg-white/10 py-3 px-4 rounded-xl text-center transition-all">
                    មុខងារ
                </Link>
                <Link :href="route('showcase.index')"
                      class="block md:inline text-sm font-khmer-Battambang text-white/80 hover:text-white hover:bg-white/10 py-3 px-4 rounded-xl text-center transition-all">
                    ម៉ឺនុយ
                </Link>
                <Link :href="route('front.promotions.index')"
                      class="block md:inline text-sm font-khmer-Battambang text-white/80 hover:text-white hover:bg-white/10 py-3 px-4 rounded-xl text-center transition-all">
                    ការផ្តល់ជូន
                </Link>
                <Link :href="route('about.index')"
                      class="block md:inline text-sm font-khmer-Battambang text-white/80 hover:text-white hover:bg-white/10 py-3 px-4 rounded-xl text-center transition-all">
                    អំពីយើង
                </Link>
                <Link :href="route('privacy.index')"
                      class="block md:inline text-sm font-khmer-Battambang text-white/80 hover:text-white hover:bg-white/10 py-3 px-4 rounded-xl text-center transition-all">
                    គោលការណ៍
                </Link>
                <Link :href="route('login')"
                      class="block md:inline text-sm font-khmer-Battambang bg-gradient-to-r from-[#F87B1B] to-pink-500 text-white hover:from-[#F87B1B]/90 hover:to-pink-600 py-3 px-6 rounded-full text-center shadow-lg hover:shadow-[#F87B1B]/50 transition-all font-bold mt-2 md:mt-0 md:ml-2">
                    ចូលគណនី
                </Link>
            </nav>

        </div>
    </div>

    <div class="h-16"></div>
</template>

<script setup>
import {ref} from 'vue';
import {Link} from "@inertiajs/vue3";

const showMenu = ref(false);

const toggleMenu = () => {
    showMenu.value = !showMenu.value;
};
</script>

<style scoped>
/* Add font awesome for mobile menu */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
</style>
