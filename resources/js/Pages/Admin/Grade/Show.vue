<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Grades", href: route("admin.grades.index")},
    {label: "Grade Details", href: "#"}
];

const props = defineProps({
    grade: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Grade Details"
            subtitle="View grade record information"
            icon="fas fa-graduation-cap"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-blue-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.grades.index')"
                        class="text-indigo-500 hover:text-indigo-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Grades
                    </Link>
                    <Link
                        :href="route('admin.grades.edit', grade?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Grade
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Information -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-user-graduate text-indigo-500"></i>
                            Student Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.student?.user?.full_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student ID</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ grade?.student?.student_id || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.student?.user?.email || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Phone Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.student?.user?.phone_number || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3">
                        <Link
                            :href="route('admin.students.show', grade?.student?.id)"
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
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.class_model?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Code</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ grade?.class_model?.code || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.class_model?.academic_year || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3">
                        <Link
                            :href="route('admin.classes.show', grade?.class_model?.id)"
                            class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-eye"></i>
                            View Full Class Details
                        </Link>
                    </div>

                    <!-- Grade Information -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Grade Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assessment Type</h3>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 capitalize">
                            {{ grade?.assessment_type || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assessment Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.assessment_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Score</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ grade?.score || 0 }} / {{ grade?.max_score || 0 }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Percentage</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.percentage || 0 }}%</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Letter Grade</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold',
                            grade?.letter_grade === 'A' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                            grade?.letter_grade === 'B' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                            grade?.letter_grade === 'C' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                            grade?.letter_grade === 'D' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                            'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            {{ grade?.letter_grade || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assessment Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ grade?.assessment_date ? new Date(grade.assessment_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Graded By</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.graded_by?.full_name || 'N/A' }}</p>
                        <p v-if="grade?.graded_by?.email" class="text-sm text-gray-600 dark:text-gray-400">{{ grade.graded_by.email }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Created At</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ grade?.created_at ? new Date(grade.created_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Last Updated</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ grade?.updated_at ? new Date(grade.updated_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                        </p>
                    </div>

                    <div v-if="grade?.comments" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Comments</h3>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ grade.comments }}</p>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

