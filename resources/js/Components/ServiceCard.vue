<template>
    <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition duration-300">
        <!-- Service Image -->
        <img :src="service.image" alt="Service Image" class="w-full h-48 object-cover rounded-tl rounded-t-md"/>

        <!-- Service Details -->
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800">{{ service.name }}</h3>
            <p class="text-sm text-gray-600 truncate mt-1">{{ service.description }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    service: Object
});
</script>

<style scoped>
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
