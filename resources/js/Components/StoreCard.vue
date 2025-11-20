<script setup>
import ImageHelper from "../Helper/ImageHelper.js";

defineProps({
    store: {
        type: Object,
        required: true
    }
});

</script>

<template>
    <div
        class="w-full bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 cursor-pointer">
        <div class="flex items-center">
            <img :src="ImageHelper.getImage(store.profile?.path)" alt="Store Profile"
                 class="w-16 h-16 rounded-full object-cover mr-4"/>
            <div class="flex flex-col">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white">{{ store.store_name }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ store.category_name }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
</style>
