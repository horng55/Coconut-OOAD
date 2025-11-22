<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "My Classes", href: route("teacher.classes.index")}];

const props = defineProps({
    classes: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const academicYear = ref(props.filters?.academic_year || "");

const handleSearch = () => {
    router.get(route('teacher.classes.index'), {
        search: searchQuery.value,
        academic_year: academicYear.value
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
                                <i class="fas fa-book text-white text-xl"></i>
                            </div>
                            My Classes
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View all your assigned classes</p>
                    </div>
                    <Link
                        :href="route('teacher.classes.create')"
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                    >
                        <i class="fas fa-plus"></i>
                        Create Class
                    </Link>
                </div>
                
                <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-6">
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search classes..."
                            class="flex-1 md:flex-none px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <input
                            v-model="academicYear"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Academic Year"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <button
                            @click="handleSearch"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                        >
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div v-if="classes?.data && classes.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="classItem in classes.data"
                        :key="classItem.id"
                        class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-all duration-200"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ classItem.name || 'N/A' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                    {{ classItem.code || 'N/A' }}
                                </p>
                            </div>
                            <span class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full">
                                {{ classItem.academic_year || 'N/A' }}
                            </span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Students:</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ classItem.enrollments?.length || 0 }} enrolled
                                </p>
                            </div>

                            <div v-if="classItem.subjects && classItem.subjects.length > 0">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Subjects:</p>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="subject in classItem.subjects"
                                        :key="subject.id"
                                        class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-xs rounded"
                                    >
                                        {{ subject.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <Link
                            :href="route('teacher.classes.show', classItem.id)"
                            class="block w-full text-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                        >
                            View Details
                        </Link>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <i class="fas fa-book text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No classes found.</p>
                </div>

                <Pagination v-if="classes?.links" :links="classes.links" class="mt-6"/>
            </div>
        </div>
    </TeacherLayout>
</template>

