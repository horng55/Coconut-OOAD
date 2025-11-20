<script setup>
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen flex flex-col items-center justify-center">
        <div class="text-center mb-6">
            <h1 class="text-8xl font-extrabold text-red-600 mb-4 tracking-wide">
                404
            </h1>
            <p class="text-lg text-black">
                Oops! The page you're looking for doesn't exist.
            </p>
            <p class="text-md text-gray-400">
                You can return to the homepage to continue exploring.
            </p>
        </div>

        <Link
            :href="route('home.index')"
            class="bg-red-500 text-white py-2 px-4 rounded-lg shadow-lg hover:bg-red-700 transition duration-300 ease-in-out transform hover:scale-105">
            Go to Homepage
        </Link>
    </div>
</template>

<style scoped>
</style>
