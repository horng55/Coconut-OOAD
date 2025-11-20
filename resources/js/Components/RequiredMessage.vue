<script setup>
const props = defineProps({
    errorMessage: {
        required: false,
        type: String,
    },
});

</script>

<template>
    <div v-if="errorMessage"
         class="text-red-500 fs-12 text-sm-start py-1"
         v-text="errorMessage"
    ></div>
</template>

<style scoped>

</style>
