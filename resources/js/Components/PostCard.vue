<script setup>
import {defineProps} from "vue";
import {Link} from "@inertiajs/vue3";
import ImageHelper from "../Helper/ImageHelper.js";

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition duration-300">
        <img :src="ImageHelper.getImage(props.post.image.path)" alt="Blog image" class="w-full h-48 object-cover">

        <div class="p-4">
            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ props.post.title }}</h2>
            <p class="text-gray-500 text-sm mb-4">{{ props.post.created_at }}</p>
            <p class="text-gray-700 text-base mb-4" v-html="props.post.content.slice(0, 120) + '...'"></p>
            <Link :href="route('blog.detail',props.post.slug)" class="text-blue-500 hover:underline">Read More</Link>
        </div>
    </div>
</template>

<style scoped>
</style>
