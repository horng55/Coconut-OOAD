<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";
import ReportHelper from "../../../Helper/ReportHelper.js";

const title = [{label: "Reports", href: route("admin.reports.index")}];

const props = defineProps({
    reports: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedReportType = ref(props.filters?.report_type || "");
const selectedStatus = ref(props.filters?.status || "");

const reportTypes = [
    { value: 'student_performance', label: 'Student Performance' },
    { value: 'class_performance', label: 'Class Performance' },
    { value: 'attendance', label: 'Attendance' },
    { value: 'grade_distribution', label: 'Grade Distribution' },
    { value: 'teacher_workload', label: 'Teacher Workload' },
    { value: 'enrollment', label: 'Enrollment' },
    { value: 'custom', label: 'Custom' },
];

const statuses = [
    { value: 'pending', label: 'Pending' },
    { value: 'generating', label: 'Generating' },
    { value: 'completed', label: 'Completed' },
    { value: 'failed', label: 'Failed' },
];

const handleSearch = () => {
    const params = {};
    if (searchQuery.value) {
        params.search = searchQuery.value;
    }
    if (selectedReportType.value) {
        params.report_type = selectedReportType.value;
    }
    if (selectedStatus.value) {
        params.status = selectedStatus.value;
    }
    
    router.get(route('admin.reports.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const deleteReport = (id) => {
    if (confirm('Are you sure you want to delete this report?')) {
        router.delete(route('admin.reports.destroy', id));
    }
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Report Management"
            :subtitle="`Total: ${reports?.total || 0} reports`"
            icon="fas fa-chart-bar"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-purple-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-2">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search reports..."
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        />
                        <button
                            @click="handleSearch"
                            class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-colors duration-200 flex items-center gap-2"
                            title="Search"
                        >
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <select
                        v-model="selectedReportType"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                    >
                        <option value="">All Types</option>
                        <option v-for="type in reportTypes" :key="type.value" :value="type.value">
                            {{ type.label }}
                        </option>
                    </select>
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                    >
                        <option value="">All Status</option>
                        <option v-for="status in statuses" :key="status.value" :value="status.value">
                            {{ status.label }}
                        </option>
                    </select>
                    <Link
                        :href="route('admin.reports.create')"
                        class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Generate Report
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Title</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Description</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Generated By</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Generated At</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(report, index) in reports?.data || []" 
                            :key="report.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-indigo-500/10 dark:bg-indigo-500/30 rounded-lg text-indigo-500 dark:text-indigo-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ report.title || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    ReportHelper.getReportTypeColor(report.report_type)
                                ]">
                                    <i class="fas fa-chart-line"></i>
                                    {{ ReportHelper.getReportTypeLabel(report.report_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ report.description || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ report.generated_by?.full_name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ report.generated_at ? new Date(report.generated_at).toLocaleString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    ReportHelper.getStatusColor(report.status)
                                ]">
                                    <i :class="ReportHelper.getStatusIcon(report.status)"></i>
                                    {{ ReportHelper.getStatusLabel(report.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.reports.show', report.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <button
                                        @click="deleteReport(report.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!reports?.data || reports.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-chart-bar text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No reports found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="reports?.links" :links="reports.links"/>
        </AdminPageWrapper>
    </App>
</template>

