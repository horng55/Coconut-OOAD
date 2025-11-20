<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Attendance", href: route("admin.attendances.index")},
    {label: "Attendance Details", href: "#"}
];

const props = defineProps({
    attendance: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Attendance Details"
            subtitle="View attendance record information"
            icon="fas fa-clipboard-check"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-pink-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.attendances.index')"
                        class="text-purple-500 hover:text-purple-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Attendance
                    </Link>
                    <Link
                        :href="route('admin.attendances.edit', attendance?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Attendance
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Information -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-user-graduate text-purple-500"></i>
                            Student Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.student?.user?.full_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student ID</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ attendance?.student?.student_id || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.student?.user?.email || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Phone Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.student?.user?.phone_number || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3">
                        <Link
                            :href="route('admin.students.show', attendance?.student?.id)"
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-eye"></i>
                            View Full Student Profile
                        </Link>
                    </div>

                    <!-- Class Information -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-book-reader text-amber-500"></i>
                            Class Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.class_model?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Code</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ attendance?.class_model?.code || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Teachers</h3>
                        <div v-if="attendance?.class_model?.teachers && attendance.class_model.teachers.length > 0" class="space-y-2">
                            <div v-for="teacher in attendance.class_model.teachers" :key="teacher.id" class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher.user?.full_name || 'N/A' }}</p>
                                    <p v-if="teacher.employee_id" class="text-sm text-gray-600 dark:text-gray-400">ID: {{ teacher.employee_id }}</p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-600 dark:text-gray-400">No teachers assigned</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.class_model?.academic_year || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3">
                        <Link
                            :href="route('admin.classes.show', attendance?.class_model?.id)"
                            class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-eye"></i>
                            View Full Class Details
                        </Link>
                    </div>

                    <!-- Attendance Information -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Attendance Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ attendance?.date ? new Date(attendance.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            attendance?.status === 'present' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : attendance?.status === 'late'
                                ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                                : attendance?.status === 'excused'
                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="
                                attendance?.status === 'present' ? 'fas fa-check-circle' :
                                attendance?.status === 'late' ? 'fas fa-clock' :
                                attendance?.status === 'excused' ? 'fas fa-info-circle' :
                                'fas fa-times-circle'
                            "></i>
                            {{ attendance?.status || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Marked By</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.marked_by?.full_name || 'N/A' }}</p>
                        <p v-if="attendance?.marked_by?.email" class="text-sm text-gray-600 dark:text-gray-400">{{ attendance.marked_by.email }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Created At</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ attendance?.created_at ? new Date(attendance.created_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                        </p>
                    </div>

                    <div v-if="attendance?.notes" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h3>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ attendance.notes }}</p>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

