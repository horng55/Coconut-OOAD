<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: 'fas fa-file'
    },
    iconColor: {
        type: String,
        default: 'text-blue-500'
    },
    iconBg: {
        type: String,
        default: 'from-blue-500/20 to-cyan-500/20'
    }
});
</script>

<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div :class="['w-12 h-12 bg-gradient-to-br rounded-xl flex items-center justify-center', iconBg]">
                        <i :class="[icon, iconColor, 'text-xl']"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ title }}</h1>
                        <p v-if="subtitle" class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ subtitle }}</p>
                    </div>
                </div>
                <div>
                    <slot name="actions"></slot>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>
</style>

