<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Announcements", href: route("teacher.announcements.index")}];

const props = defineProps({
    announcements: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");

const handleSearch = () => {
    router.get(route('teacher.announcements.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-bullhorn text-white text-xl"></i>
                            </div>
                            Announcements
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View all announcements</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search announcements..."
                            class="flex-1 md:flex-none px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <select
                            v-model="selectedClass"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div v-if="announcements?.data && announcements.data.length > 0" class="space-y-4">
                    <Link
                        v-for="announcement in announcements.data"
                        :key="announcement.id"
                        :href="route('teacher.announcements.show', announcement.id)"
                        class="block bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                    {{ announcement.title || 'N/A' }}
                                </h3>
                                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    <span v-if="announcement.class_model" class="flex items-center gap-1">
                                        <i class="fas fa-book"></i>
                                        {{ announcement.class_model.name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-calendar"></i>
                                        {{ announcement.publish_date || 'N/A' }}
                                    </span>
                                    <span v-if="announcement.created_by_user" class="flex items-center gap-1">
                                        <i class="fas fa-user"></i>
                                        {{ announcement.created_by_user.full_name }}
                                    </span>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 line-clamp-2">
                                    {{ announcement.content || 'No content' }}
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 ml-4"></i>
                        </div>
                    </Link>
                </div>

                <div v-else class="text-center py-12">
                    <i class="fas fa-bullhorn text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No announcements found.</p>
                </div>

                <Pagination v-if="announcements?.links" :links="announcements.links" class="mt-6"/>
            </div>
        </div>
    </TeacherLayout>
</template>

