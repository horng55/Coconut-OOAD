<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "My Classes", href: route("student.classes.index")}];

const props = defineProps({
    enrollments: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const academicYear = ref(props.filters?.academic_year || "");

const handleSearch = () => {
    router.get(route('student.classes.index'), {
        search: searchQuery.value,
        academic_year: academicYear.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <StudentLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book text-white text-xl"></i>
                            </div>
                            My Classes
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View all your enrolled classes</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search classes..."
                            class="flex-1 md:flex-none px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <input
                            v-model="academicYear"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Academic Year"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <button
                            @click="handleSearch"
                            class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors"
                        >
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div v-if="enrollments?.data && enrollments.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="enrollment in enrollments.data"
                        :key="enrollment.id"
                        class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-emerald-200 dark:border-emerald-800 hover:shadow-lg transition-all duration-200"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ enrollment.class_model?.name || 'N/A' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                    {{ enrollment.class_model?.code || 'N/A' }}
                                </p>
                            </div>
                            <span class="px-3 py-1 bg-emerald-500 text-white text-xs font-semibold rounded-full">
                                {{ enrollment.class_model?.academic_year || 'N/A' }}
                            </span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div v-if="enrollment.class_model?.teachers && enrollment.class_model.teachers.length > 0">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Teachers:</p>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="teacher in enrollment.class_model.teachers"
                                        :key="teacher.id"
                                        class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs rounded"
                                    >
                                        {{ teacher.user?.full_name || 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="enrollment.class_model?.subjects && enrollment.class_model.subjects.length > 0">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Subjects:</p>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="subject in enrollment.class_model.subjects"
                                        :key="subject.id"
                                        class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-xs rounded"
                                    >
                                        {{ subject.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <Link
                            :href="route('student.classes.show', enrollment.id)"
                            class="block w-full text-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors"
                        >
                            View Details
                        </Link>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <i class="fas fa-book text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No classes found.</p>
                </div>

                <Pagination v-if="enrollments?.links" :links="enrollments.links" class="mt-6"/>
            </div>
        </div>
    </StudentLayout>
</template>

