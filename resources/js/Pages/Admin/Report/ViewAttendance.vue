<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Reports", href: route("admin.reports.index")},
    {label: "Attendance Report", href: "#"}
];

const props = defineProps({
    report: Object,
    attendances: Array,
    statistics: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Attendance Report"
            subtitle="Attendance statistics and records"
            icon="fas fa-calendar-check"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.reports.index')"
                        class="text-blue-500 hover:text-blue-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Reports
                    </Link>
                </div>

                <!-- Statistics Summary -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Records</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ statistics?.total_records || 0 }}</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Present</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ statistics?.present_count || 0 }}</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Absent</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ statistics?.absent_count || 0 }}</p>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Late</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ statistics?.late_count || 0 }}</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Present Rate</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ statistics?.present_rate || 0 }}%</p>
                    </div>
                </div>

                <!-- Attendance Records Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attendance Records</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="attendance in attendances" :key="attendance.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ attendance.date ? new Date(attendance.date).toLocaleDateString() : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ attendance.student?.user?.full_name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ attendance.class_model?.name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span :class="[
                                            'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                            attendance.status === 'present' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                            attendance.status === 'absent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                            attendance.status === 'late' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                            'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                        ]">
                                            {{ attendance.status ? attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1) : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ attendance.notes || '-' }}
                                    </td>
                                </tr>
                                <tr v-if="attendances.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No attendance records found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

