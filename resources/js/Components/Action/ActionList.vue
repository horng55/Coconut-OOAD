<script setup>
import ActionItem from "./ActionItem.vue";

const props = defineProps({
    actionItem: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-wrap justify-center gap-2">
        <ActionItem
            v-for="(item, idx) in actionItem"
            :key="idx"
            :icon_class="item.icon_class"
            :title="item.title"
            :href="item.href"
            :click="item.click"
            :color="item.color"
        />
    </div>
</template>
