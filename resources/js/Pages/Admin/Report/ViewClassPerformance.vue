<script setup>
import {computed} from "vue";
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Reports", href: route("admin.reports.index")},
    {label: "Class Performance Report", href: "#"}
];

const props = defineProps({
    report: Object,
    classData: Object,
    statistics: Array,
});

const reportTitle = computed(() => {
    return `Class Performance Report: ${props.classData?.name || 'N/A'}`;
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="reportTitle"
            subtitle="Overall class performance with student rankings"
            icon="fas fa-users"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.reports.index')"
                        class="text-green-500 hover:text-green-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Reports
                    </Link>
                </div>

                <!-- Class Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Class Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Class Name</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ classData?.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Class Code</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ classData?.code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Academic Year</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ classData?.academic_year }}</p>
                        </div>
                    </div>
                </div>

                <!-- Student Performance Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Student Performance Rankings</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Rank</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Average Score</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Total Assessments</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Attendance Rate</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Present Days</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="(stat, index) in statistics" :key="stat.student?.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-semibold"
                                            :class="index === 0 ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                                    index === 1 ? 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300' :
                                                    index === 2 ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                                    'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'">
                                            {{ index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-medium">
                                        {{ stat.student?.user?.full_name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ stat.student?.student_id || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-semibold"
                                            :class="stat.average_score >= 80 ? 'text-green-600 dark:text-green-400' :
                                                    stat.average_score >= 60 ? 'text-yellow-600 dark:text-yellow-400' :
                                                    'text-red-600 dark:text-red-400'">
                                            {{ stat.average_score }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ stat.total_assessments }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-semibold"
                                            :class="stat.attendance_rate >= 90 ? 'text-green-600 dark:text-green-400' :
                                                    stat.attendance_rate >= 75 ? 'text-yellow-600 dark:text-yellow-400' :
                                                    'text-red-600 dark:text-red-400'">
                                            {{ stat.attendance_rate }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ stat.present_days }}/{{ stat.total_days }}
                                    </td>
                                </tr>
                                <tr v-if="statistics.length === 0">
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No student data found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

