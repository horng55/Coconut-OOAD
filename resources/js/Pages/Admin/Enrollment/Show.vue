<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Enrollments", href: route("admin.enrollments.index")},
    {label: "Enrollment Details", href: "#"}
];

const props = defineProps({
    enrollment: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Enrollment Details"
            subtitle="View enrollment information and related details"
            icon="fas fa-clipboard-list"
            icon-color="text-teal-500"
            icon-bg="from-teal-500/20 to-cyan-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.enrollments.index')"
                        class="text-teal-500 hover:text-teal-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Enrollments
                    </Link>
                    <Link
                        :href="route('admin.enrollments.edit', enrollment?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Enrollment
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Information -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-user-graduate text-teal-500"></i>
                            Student Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ enrollment?.student?.user?.full_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student ID</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ enrollment?.student?.student_id || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ enrollment?.student?.user?.email || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Phone Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ enrollment?.student?.user?.phone_number || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3">
                        <Link
                            :href="route('admin.students.show', enrollment?.student?.id)"
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
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ enrollment?.class_model?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Code</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ enrollment?.class_model?.code || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Teachers</h3>
                        <div v-if="enrollment?.class_model?.teachers && enrollment.class_model.teachers.length > 0" class="space-y-2">
                            <div v-for="teacher in enrollment.class_model.teachers" :key="teacher.id" class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded">
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
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ enrollment?.class_model?.academic_year || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3">
                        <Link
                            v-if="enrollment?.class_model?.id"
                            :href="route('admin.classes.show', { class: enrollment.class_model.id })"
                            class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-eye"></i>
                            View Full Class Details
                        </Link>
                    </div>

                    <!-- Enrollment Information -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Enrollment Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Enrollment Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ enrollment?.enrollment_date ? new Date(enrollment.enrollment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            enrollment?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : enrollment?.status === 'completed'
                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="enrollment?.status === 'active' ? 'fas fa-check-circle' : enrollment?.status === 'completed' ? 'fas fa-check-double' : 'fas fa-times-circle'"></i>
                            {{ enrollment?.status || 'N/A' }}
                        </span>
                    </div>

                    <div v-if="enrollment?.notes" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h3>
                        <p class="text-gray-900 dark:text-white">{{ enrollment.notes }}</p>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

