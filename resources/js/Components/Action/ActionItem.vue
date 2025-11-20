<script setup>
import {Link} from "@inertiajs/vue3";
import ActionIcon from "./ActionIcon.vue";

const props = defineProps({
    title: {type: String, required: true},
    icon_class: {type: String, required: true},
    href: {type: String, required: false},
    click: {type: Function, required: false},
    color: {type: String, default: "blue"},
});

import {computed} from "vue";

const baseClasses = "inline-flex items-center gap-1 px-3 py-1 rounded text-white text-sm font-medium";

const bgClass = computed(() => {
    switch (props.color) {
        case "red":
            return "bg-red-500 hover:bg-red-600";
        case "green":
            return "bg-green-500 hover:bg-green-600";
        case "yellow":
            return "bg-yellow-500 hover:bg-yellow-600";
        case "gray":
            return "bg-gray-500 hover:bg-gray-600";
        default:
            return "bg-blue-500 hover:bg-blue-600";
    }
});
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :class="`${baseClasses} ${bgClass}`"
        role="menuitem"
    >
        <ActionIcon :icon="icon_class"/>
        <span>{{ title }}</span>
    </Link>

    <button
        v-else-if="click"
        @click="click"
        type="button"
        :class="`${baseClasses} ${bgClass}`"
        role="menuitem"
    >
        <ActionIcon :icon="icon_class"/>
        <span class="font-khmer-Battambang">{{ title }}</span>
    </button>
</template>
