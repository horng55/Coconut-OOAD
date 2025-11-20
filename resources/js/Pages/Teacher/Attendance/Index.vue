<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Attendance", href: route("teacher.attendances.index")}];

const props = defineProps({
    attendances: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");
const selectedDate = ref(props.filters?.date || "");
const selectedStatus = ref(props.filters?.status || "");

const handleSearch = () => {
    router.get(route('teacher.attendances.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value,
        date: selectedDate.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-clipboard-check"></i>
                                Attendance Management
                            </h1>
                            <p class="text-purple-100 mt-1">Total: {{ attendances?.total || 0 }} attendance records</p>
                        </div>
                        <Link
                            :href="route('teacher.attendances.create')"
                            class="bg-white text-purple-600 hover:bg-purple-50 px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 font-medium shadow-lg hover:shadow-xl"
                        >
                            <i class="fas fa-plus"></i>
                            Mark Attendance
                        </Link>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-3 flex-wrap mb-6">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search student..."
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        />
                        <select
                            v-model="selectedClass"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }})
                            </option>
                        </select>
                        <input
                            v-model="selectedDate"
                            @change="handleSearch"
                            type="date"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        />
                        <select
                            v-model="selectedStatus"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        >
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="excused">Excused</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Notes</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr 
                                    v-for="(attendance, index) in attendances?.data || []" 
                                    :key="attendance.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                                >
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center justify-center w-8 h-8 bg-purple-500/10 dark:bg-purple-500/30 rounded-lg text-purple-500 dark:text-purple-400 font-semibold">
                                            {{ index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ attendance.student?.user?.full_name || 'N/A' }}</span>
                                            <span v-if="attendance.student?.student_id" class="text-gray-600 dark:text-gray-400 text-xs block">ID: {{ attendance.student.student_id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ attendance.class_model?.name || 'N/A' }}</span>
                                            <span v-if="attendance.class_model?.code" class="text-gray-600 dark:text-gray-400 text-xs block">Code: {{ attendance.class_model.code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ attendance.date ? new Date(attendance.date).toLocaleDateString() : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                            attendance.status === 'present' 
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                                : attendance.status === 'late'
                                                ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                                                : attendance.status === 'excused'
                                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                        ]">
                                            <i :class="
                                                attendance.status === 'present' ? 'fas fa-check-circle' :
                                                attendance.status === 'late' ? 'fas fa-clock' :
                                                attendance.status === 'excused' ? 'fas fa-info-circle' :
                                                'fas fa-times-circle'
                                            "></i>
                                            {{ attendance.status || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ attendance.notes || '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link
                                                :href="route('teacher.attendances.edit', attendance.id)"
                                                class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Edit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!attendances?.data || attendances.data.length === 0">
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-clipboard-check text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                            <p>No attendance records found.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination v-if="attendances?.links" :links="attendances.links"/>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>

