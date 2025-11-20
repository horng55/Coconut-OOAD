<script setup>
import FrontHeader from "../Components/FrontHeader.vue";
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center bg-black transition duration-300">

        <FrontHeader/>

        <div class="w-full flex-grow">
            <slot name="content"></slot>
        </div>

        <footer class="relative w-full overflow-hidden mt-auto">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-black to-purple-950"></div>
            <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>

            <div class="relative z-10 border-t border-white/10 py-16">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                        <!-- Company Info -->
                        <div class="col-span-1 md:col-span-2 text-center md:text-left">
                            <div class="flex items-center justify-center md:justify-start gap-3 mb-6">
                                <div class="relative w-14 h-14 rounded-2xl overflow-hidden shadow-lg ring-2 ring-white/10 bg-white">
                                    <img
                                        src="/cth_logo.png"
                                        alt="Chhin Taing Logo"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <h3 class="text-2xl font-khmer-Moul bg-gradient-to-r from-[#FF9013] to-pink-400 bg-clip-text text-transparent">
                                    ចុងភៅ ឈិន តាំង
                                </h3>
                            </div>
                            <p class="text-white/60 font-khmer-Battambang text-sm leading-relaxed max-w-md mx-auto md:mx-0">
                                មានទទួលរៀបចំកម្មវិធីតូចធំទៅតាមតម្រូវការអតិថិជន ដែលផ្តោតលើគុណភាព<br>ម្ហូបឆ្ងាញ់ សេវាកម្មល្អ និងរហ័សទាន់ចិត្ត ជាមួយនឹងបទពិសោធន៍ជាច្រើនឆ្នាំ<br>ក្នុងវិស័យចុងភៅ
                            </p>
                        </div>

                        <!-- Quick Links -->
                        <div class="text-center md:text-left">
                            <h4 class="text-lg font-bold mb-6 text-white font-khmer-Battambang">តំណភ្ជាប់រហ័ស</h4>
                            <ul class="space-y-3">
                                <li><Link :href="route('home.index')" class="text-white/60 hover:text-white transition-colors font-khmer-Battambang text-sm hover:translate-x-1 inline-block transform transition-transform duration-200">→ ទំព័រដើម</Link></li>
                                <li><Link :href="route('about.index')" class="text-white/60 hover:text-white transition-colors font-khmer-Battambang text-sm hover:translate-x-1 inline-block transform transition-transform duration-200">→ អំពីយើង</Link></li>
                                <li><Link :href="route('function.index')" class="text-white/60 hover:text-white transition-colors font-khmer-Battambang text-sm hover:translate-x-1 inline-block transform transition-transform duration-200">→ មុខងារ</Link></li>
                                <li><Link :href="route('contact.index')" class="text-white/60 hover:text-white transition-colors font-khmer-Battambang text-sm hover:translate-x-1 inline-block transform transition-transform duration-200">→ ទំនាក់ទំនង</Link></li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-white/40 font-khmer-Battambang text-sm">
                            រក្សាសិទិ្ធដោយ © {{ new Date().getFullYear() }} ចុងភៅឈិនតាំង | All rights reserved
                        </p>
                        <a href="https://socheaofficial.com" target="_blank" rel="noopener noreferrer" class="text-white/30 hover:text-white/50 text-xs transition-colors duration-200">
                            Powered by socheaofficial.com ✨
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Grid Pattern */
.bg-grid-pattern {
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
}

/* Glassmorphism effect enhancement */
.backdrop-blur-xl {
    -webkit-backdrop-filter: blur(24px);
    backdrop-filter: blur(24px);
}

/* Text gradient support */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
