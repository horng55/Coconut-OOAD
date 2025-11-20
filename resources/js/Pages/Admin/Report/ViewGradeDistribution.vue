<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Reports", href: route("admin.reports.index")},
    {label: "Grade Distribution Report", href: "#"}
];

const props = defineProps({
    report: Object,
    grades: Array,
    distribution: Object,
    statistics: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Grade Distribution Report"
            subtitle="Grade distribution and statistics"
            icon="fas fa-chart-pie"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-pink-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.reports.index')"
                        class="text-purple-500 hover:text-purple-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Reports
                    </Link>
                </div>

                <!-- Statistics Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Grades</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ statistics?.total_grades || 0 }}</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Average Score</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ statistics?.average_score || 0 }}%</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Highest Score</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ statistics?.highest_score || 0 }}%</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Lowest Score</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ statistics?.lowest_score || 0 }}%</p>
                    </div>
                </div>

                <!-- Grade Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Grade Distribution</h2>
                    <div class="grid grid-cols-5 gap-4">
                        <div v-for="(count, grade) in distribution" :key="grade" class="text-center">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-3xl font-bold mb-2"
                                    :class="grade === 'A' ? 'text-green-600 dark:text-green-400' :
                                            grade === 'B' ? 'text-blue-600 dark:text-blue-400' :
                                            grade === 'C' ? 'text-yellow-600 dark:text-yellow-400' :
                                            grade === 'D' ? 'text-orange-600 dark:text-orange-400' :
                                            'text-red-600 dark:text-red-400'">
                                    {{ grade }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ count }} students</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grades Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">All Grades</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Assessment</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Score</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Percentage</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="grade in grades" :key="grade.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ grade.student?.user?.full_name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ grade.class_model?.name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ grade.assessment_name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ grade.assessment_type || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ grade.score }}/{{ grade.max_score }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ grade.percentage }}%
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                            {{ grade.letter_grade || 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="grades.length === 0">
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No grades found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

