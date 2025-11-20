<template>
    <div class="relative group">
        <!-- Main Icon Button -->
        <Link :href="href"
              class="flex items-center justify-center w-12 h-12 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-all"
              :class="{ 'active': active }">
            <slot name="icon"/>
        </Link>

        class="absolute left-full ml-2 mt-2 bg-gray-800 text-white rounded-lg shadow-lg w-48 hidden group-hover:block
        transition-opacity duration-200 z-50">
        <ul class="py-2">
            <li v-for="submenuItem in item" :key="submenuItem.title">
                <Link :href="submenuItem.href"
                      class="flex items-center px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700 transition-all"
                      :class="{ 'active-sub': submenuItem.active }">
                    <i class="fa-solid fa-circle text-xs mr-2"/>
                    {{ submenuItem.title }}
                </Link>
            </li>
        </ul>
    </div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';

defineProps({
    href: String,
    item: Array,
    active: Boolean,
});
</script>

<style scoped>
.active {
    background-color: #2563eb;
    color: white;
}

.group:hover .dropdown-menu {
    display: block;
}
</style>
