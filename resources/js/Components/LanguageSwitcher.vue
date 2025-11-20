<script setup>
import {ref, onMounted} from "vue";
import {useGlobalStore} from "../Global/Global.js";
import {router, useForm} from "@inertiajs/vue3";

const store = useGlobalStore();
const showDropdown = ref(false);

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const changeLanguage = async (lang) => {
    try {
        const form = useForm({ locale: lang });
        form.post(route('language.switch'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                store.changeLanguage(lang);
                showDropdown.value = false;
            },
            onError: (errors) => {
                console.error("Failed to change language:", errors);
            }
        });
    } catch (error) {
        console.error("Error changing language:", error);
    }
};

// Close dropdown when clicking outside
onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.language-switcher')) {
            showDropdown.value = false;
        }
    });
});
</script>

<template>
    <div class="relative language-switcher">
        <button @click="toggleDropdown"
                class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            <i class="fas fa-globe text-sm"></i>
            <span>{{ store.locale === 'en' ? 'English' : 'ខ្មែរ' }}</span>
            <i class="fa-solid fa-angle-down text-xs"></i>
        </button>

        <div v-show="showDropdown"
             class="absolute right-0 mt-2 bg-white dark:bg-gray-800 shadow-lg rounded-lg w-32 p-2 border border-gray-200 dark:border-gray-700 transition-opacity duration-300 z-50">
            <button @click="changeLanguage('en')"
                    :class="[
                        'block w-full text-left px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition',
                        store.locale === 'en' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-300'
                    ]">
                <i class="fas fa-language mr-2"></i>English
            </button>
            <button @click="changeLanguage('kh')"
                    :class="[
                        'block w-full text-left px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition',
                        store.locale === 'kh' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-300'
                    ]">
                <i class="fas fa-language mr-2"></i>ខ្មែរ
            </button>
        </div>
    </div>
</template>

<style scoped>
</style>
