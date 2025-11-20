<script setup>
defineProps({
    icon: {type: String, required: true},
});
</script>

<template>
    <i :class="icon" aria-hidden="true"></i>
</template>
