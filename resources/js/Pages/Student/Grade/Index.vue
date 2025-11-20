<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "My Grades", href: route("student.grades.index")}];

const props = defineProps({
    grades: Object,
    classes: Array,
    stats: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");
const academicYear = ref(props.filters?.academic_year || "");

const handleSearch = () => {
    router.get(route('student.grades.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value,
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
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm mb-1">Total Grades</p>
                            <p class="text-3xl font-bold">{{ stats?.total_grades || 0 }}</p>
                        </div>
                        <i class="fas fa-graduation-cap text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Average Grade</p>
                            <p class="text-3xl font-bold">{{ Math.round(stats?.average_percentage || 0) }}%</p>
                        </div>
                        <i class="fas fa-chart-line text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm mb-1">Highest Grade</p>
                            <p class="text-3xl font-bold">{{ Math.round(stats?.highest_grade || 0) }}%</p>
                        </div>
                        <i class="fas fa-arrow-up text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm mb-1">Lowest Grade</p>
                            <p class="text-3xl font-bold">{{ Math.round(stats?.lowest_grade || 0) }}%</p>
                        </div>
                        <i class="fas fa-arrow-down text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Grades List -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-xl"></i>
                            </div>
                            My Grades
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View all your grades and assessments</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search..."
                            class="flex-1 md:flex-none px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <select
                            v-model="selectedClass"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }}
                            </option>
                        </select>
                        <input
                            v-model="academicYear"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Academic Year"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div v-if="grades?.data && grades.data.length > 0" class="space-y-4">
                    <div
                        v-for="grade in grades.data"
                        :key="grade.id"
                        class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200"
                    >
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ grade.assessment?.assessment_name || grade.assessment_name || 'N/A' }}
                                    </h3>
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-semibold rounded-full">
                                        {{ grade.assessment?.assessment_type || grade.assessment_type || 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-book"></i>
                                        {{ grade.class_model?.name || 'N/A' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-calendar"></i>
                                        {{ grade.assessment_date ? new Date(grade.assessment_date).toLocaleDateString() : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Score</p>
                                    <p class="text-2xl font-bold" :class="[
                                        grade.percentage >= 80 ? 'text-green-600 dark:text-green-400' :
                                        grade.percentage >= 60 ? 'text-blue-600 dark:text-blue-400' :
                                        grade.percentage >= 40 ? 'text-amber-600 dark:text-amber-400' :
                                        'text-red-600 dark:text-red-400'
                                    ]">
                                        {{ grade.score || 0 }}/{{ grade.assessment?.max_score || grade.max_score || 0 }}
                                    </p>
                                    <p class="text-sm font-semibold" :class="[
                                        grade.percentage >= 80 ? 'text-green-600 dark:text-green-400' :
                                        grade.percentage >= 60 ? 'text-blue-600 dark:text-blue-400' :
                                        grade.percentage >= 40 ? 'text-amber-600 dark:text-amber-400' :
                                        'text-red-600 dark:text-red-400'
                                    ]">
                                        {{ Math.round(grade.percentage || 0) }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <i class="fas fa-graduation-cap text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No grades found.</p>
                </div>

                <Pagination v-if="grades?.links" :links="grades.links" class="mt-6"/>
            </div>
        </div>
    </StudentLayout>
</template>

