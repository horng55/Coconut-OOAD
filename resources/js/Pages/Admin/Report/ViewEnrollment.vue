<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Reports", href: route("admin.reports.index")},
    {label: "Enrollment Report", href: "#"}
];

const props = defineProps({
    report: Object,
    enrollments: Array,
    classStats: Array,
    totalEnrollments: Number,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Enrollment Report"
            subtitle="Student enrollment statistics"
            icon="fas fa-user-plus"
            icon-color="text-cyan-500"
            icon-bg="from-cyan-500/20 to-blue-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.reports.index')"
                        class="text-cyan-500 hover:text-cyan-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Reports
                    </Link>
                </div>

                <!-- Summary -->
                <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Total Enrollments</h2>
                    <p class="text-3xl font-bold text-cyan-600 dark:text-cyan-400">{{ totalEnrollments || 0 }}</p>
                </div>

                <!-- Class Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Enrollment by Class</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Code</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Enrolled</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Max Capacity</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Capacity %</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="stat in classStats" :key="stat.class?.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-medium">
                                        {{ stat.class?.name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ stat.class?.code || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ stat.total_students }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ stat.max_students || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-cyan-500 h-2 rounded-full"
                                                    :style="`width: ${Math.min(stat.capacity_percentage, 100)}%`"
                                                    :class="stat.capacity_percentage >= 90 ? 'bg-red-500' :
                                                            stat.capacity_percentage >= 75 ? 'bg-yellow-500' :
                                                            'bg-green-500'">
                                                </div>
                                            </div>
                                            <span class="text-sm font-medium"
                                                :class="stat.capacity_percentage >= 90 ? 'text-red-600 dark:text-red-400' :
                                                        stat.capacity_percentage >= 75 ? 'text-yellow-600 dark:text-yellow-400' :
                                                        'text-green-600 dark:text-green-400'">
                                                {{ stat.capacity_percentage }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="classStats.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No class statistics found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Enrollment Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Enrollment Details</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Enrollment Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="enrollment in enrollments" :key="enrollment.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ enrollment.student?.user?.full_name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ enrollment.student?.student_id || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ enrollment.class_model?.name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ enrollment.enrollment_date ? new Date(enrollment.enrollment_date).toLocaleDateString() : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            {{ enrollment.status ? enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1) : 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="enrollments.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No enrollments found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

