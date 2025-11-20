<template>
    <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition duration-300">
        <!-- Product Image -->
        <img :src="product.image" alt="Product Image" class="w-full h-48 object-cover rounded-tl rounded-t-md"/>

        <!-- Product Details -->
        <div class="p-4">
            <h3 class="text-xl font-semibold text-gray-800">{{ product.name }}</h3>
            <p class="text-lg text-gray-600 truncate">{{ product.description }}</p>
            <p class="text-lg text-gray-800 font-semibold">${{ product.price }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    product: Object
});
</script>

<style scoped>
/* Add the truncation for the description to limit it to one line */
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
