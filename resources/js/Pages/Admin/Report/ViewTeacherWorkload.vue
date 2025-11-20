<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Reports", href: route("admin.reports.index")},
    {label: "Teacher Workload Report", href: "#"}
];

const props = defineProps({
    report: Object,
    workloadData: Array,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Teacher Workload Report"
            subtitle="Teacher workload and class assignments"
            icon="fas fa-chalkboard-teacher"
            icon-color="text-orange-500"
            icon-bg="from-orange-500/20 to-red-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.reports.index')"
                        class="text-orange-500 hover:text-orange-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Reports
                    </Link>
                </div>

                <!-- Teacher Workload Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Teacher Workload</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Teacher</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Employee ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Total Classes</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Total Students</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Classes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="workload in workloadData" :key="workload.teacher?.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-medium">
                                        {{ workload.teacher?.user?.full_name || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ workload.teacher?.employee_id || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ workload.total_classes }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            {{ workload.total_students }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            <span v-for="classItem in workload.classes" :key="classItem.id"
                                                class="px-2 py-1 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                {{ classItem.name }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="workloadData.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No teacher data found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

