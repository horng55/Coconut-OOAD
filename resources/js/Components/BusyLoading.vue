<script setup>

</script>

<template>
    <div class="busy-loading-container">
        <div class="loader"></div>
    </div>
</template>

<style scoped>
.busy-loading-container {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.5);
}

.loader {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #33bdf1;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
