<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Announcements", href: route("admin.announcements.index")}];

const props = defineProps({
    announcements: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");
const selectedAudience = ref(props.filters?.target_audience || "");

const handleSearch = () => {
    router.get(route('admin.announcements.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value,
        target_audience: selectedAudience.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteAnnouncement = (id) => {
    if (confirm('Are you sure you want to delete this announcement?')) {
        router.delete(route('admin.announcements.destroy', id));
    }
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Announcements Management"
            :subtitle="`Total: ${announcements?.total || 0} announcements`"
            icon="fas fa-bullhorn"
            icon-color="text-rose-500"
            icon-bg="from-rose-500/20 to-pink-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search announcements..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                    />
                    <select
                        v-model="selectedClass"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                    >
                        <option value="">All Classes</option>
                        <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                            {{ classItem.name }} ({{ classItem.code }})
                        </option>
                    </select>
                    <select
                        v-model="selectedAudience"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                    >
                        <option value="">All Audiences</option>
                        <option value="all">All</option>
                        <option value="teachers">Teachers</option>
                        <option value="students">Students</option>
                        <option value="parents">Parents</option>
                    </select>
                    <Link
                        :href="route('admin.announcements.create')"
                        class="bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        New Announcement
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-rose-500/10 to-pink-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Title</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Target Audience</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Priority</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Publish Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Created By</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(announcement, index) in announcements?.data || []" 
                            :key="announcement.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-rose-500/10 dark:bg-rose-500/30 rounded-lg text-rose-500 dark:text-rose-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ announcement.title || 'N/A' }}</span>
                                    <span v-if="announcement.is_pinned" class="ml-2 text-rose-500">
                                        <i class="fas fa-thumbtack" title="Pinned"></i>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ announcement.class_model?.name || 'All Classes' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="audience in (Array.isArray(announcement.target_audience) ? announcement.target_audience : [announcement.target_audience])" 
                                        :key="audience"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium capitalize"
                                    >
                                        {{ audience }}
                                    </span>
                                    <span v-if="!announcement.target_audience || (Array.isArray(announcement.target_audience) && announcement.target_audience.length === 0)" class="text-gray-500 dark:text-gray-400 text-xs">
                                        N/A
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    announcement.priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                    announcement.priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                    announcement.priority === 'normal' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                    'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="
                                        announcement.priority === 'urgent' ? 'fas fa-exclamation-triangle' :
                                        announcement.priority === 'high' ? 'fas fa-exclamation-circle' :
                                        'fas fa-info-circle'
                                    "></i>
                                    {{ announcement.priority || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ announcement.publish_date ? new Date(announcement.publish_date).toLocaleDateString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ announcement.created_by_user?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.announcements.show', announcement.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.announcements.edit', announcement.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button
                                        @click="deleteAnnouncement(announcement.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!announcements?.data || announcements.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-bullhorn text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No announcements found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="announcements?.links" :links="announcements.links"/>
        </AdminPageWrapper>
    </App>
</template>

