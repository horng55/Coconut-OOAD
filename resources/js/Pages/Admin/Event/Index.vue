<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Events", href: route("admin.events.index")}];

const props = defineProps({
    paginate: Object,
    search: String,
});

const searchQuery = ref(props.search || "");

const handleSearch = () => {
    router.get(route('admin.events.index'), { search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const parseBackground = (background) => {
    if (!background) return null;
    if (typeof background === 'object') return background;
    try {
        return JSON.parse(background);
    } catch {
        return null;
    }
};

const getEventTypeLabel = (type) => {
    const types = {
        'wedding': 'Wedding',
        'birthday': 'Birthday',
        'housing': 'Housing',
        'party': 'Party',
    };
    return types[type] || type;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Events Management"
            :subtitle="`Total: ${paginate?.total || 0} events from all users`"
            icon="fas fa-calendar-alt"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search events..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                    />
                </div>
            </template>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-calendar mr-2 text-blue-500"></i>Event Name
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-user mr-2 text-blue-500"></i>User
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-tag mr-2 text-blue-500"></i>Type
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-phone mr-2 text-blue-500"></i>Phone
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Location
                            </th>
                            <th class=" text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-calendar-day mr-2 text-blue-500"></i>Date
                            </th>
                            <th class=" text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-cog mr-2 text-gray-500"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(item, index) in paginate?.data || []" 
                            :key="item.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-500/10 dark:bg-blue-500/30 rounded-lg text-blue-500 dark:text-blue-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.name }}</span>
                                    <span v-if="item.additional_name" class="text-gray-600 dark:text-gray-400 text-sm block">{{ item.additional_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.user_name || item.user_username || 'N/A' }}</span>
                                    <span v-if="item.user_email" class="text-gray-600 dark:text-gray-400 text-xs block">{{ item.user_email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full text-xs font-medium">
                                    <i class="fas fa-tag"></i>
                                    {{ getEventTypeLabel(item.type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ item.phone || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ item.location || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ formatDate(item.date) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.events.show', item.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2"
                                        title="View Event"
                                    >
                                        <i class="fas fa-eye"></i>
                                        View
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!paginate?.data || paginate.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-times text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No events found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="paginate?.links" :links="paginate.links"/>
        </AdminPageWrapper>
    </App>
</template>

<style scoped>
</style>

