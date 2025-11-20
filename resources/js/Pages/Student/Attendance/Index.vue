<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "My Attendance", href: route("student.attendances.index")}];

const props = defineProps({
    attendances: Object,
    classes: Array,
    stats: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");
const selectedStatus = ref(props.filters?.status || "");
const dateFrom = ref(props.filters?.date_from || "");
const dateTo = ref(props.filters?.date_to || "");

const handleSearch = () => {
    router.get(route('student.attendances.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value,
        status: selectedStatus.value,
        date_from: dateFrom.value,
        date_to: dateTo.value
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
                            <p class="text-emerald-100 text-sm mb-1">Total Attendance</p>
                            <p class="text-3xl font-bold">{{ stats?.total_attendance || 0 }}</p>
                        </div>
                        <i class="fas fa-clipboard-check text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm mb-1">Present</p>
                            <p class="text-3xl font-bold">{{ stats?.present_count || 0 }}</p>
                        </div>
                        <i class="fas fa-check-circle text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-pink-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm mb-1">Absent</p>
                            <p class="text-3xl font-bold">{{ stats?.absent_count || 0 }}</p>
                        </div>
                        <i class="fas fa-times-circle text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm mb-1">Attendance Rate</p>
                            <p class="text-3xl font-bold">{{ stats?.attendance_percentage || 0 }}%</p>
                        </div>
                        <i class="fas fa-percentage text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Attendance List -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-white text-xl"></i>
                            </div>
                            My Attendance
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View your attendance records</p>
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
                        <select
                            v-model="selectedStatus"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        >
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                        <input
                            v-model="dateFrom"
                            @change="handleSearch"
                            type="date"
                            placeholder="From Date"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <input
                            v-model="dateTo"
                            @change="handleSearch"
                            type="date"
                            placeholder="To Date"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div v-if="attendances?.data && attendances.data.length > 0" class="space-y-4">
                    <div
                        v-for="attendance in attendances.data"
                        :key="attendance.id"
                        class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200"
                    >
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ attendance.class_model?.name || 'N/A' }}
                                    </h3>
                                    <span :class="[
                                        'px-3 py-1 text-xs font-semibold rounded-full',
                                        attendance.status === 'present' 
                                            ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                            : attendance.status === 'late'
                                            ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
                                            : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                    ]">
                                        <i :class="[
                                            attendance.status === 'present' ? 'fas fa-check-circle' :
                                            attendance.status === 'late' ? 'fas fa-clock' :
                                            'fas fa-times-circle'
                                        ]"></i>
                                        {{ attendance.status || 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-calendar"></i>
                                        {{ attendance.date || 'N/A' }}
                                    </span>
                                    <span v-if="attendance.notes" class="flex items-center gap-1">
                                        <i class="fas fa-sticky-note"></i>
                                        {{ attendance.notes }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <i class="fas fa-clipboard-check text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No attendance records found.</p>
                </div>

                <Pagination v-if="attendances?.links" :links="attendances.links" class="mt-6"/>
            </div>
        </div>
    </StudentLayout>
</template>

