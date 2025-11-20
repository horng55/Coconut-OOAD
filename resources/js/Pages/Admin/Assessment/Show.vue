<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Assessments", href: route("admin.assessments.index")},
    {label: "Assessment Details", href: "#"}
];

const props = defineProps({
    assessment: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Assessment Details"
            subtitle="View assessment information"
            icon="fas fa-clipboard-list"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-purple-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.assessments.index')"
                        class="text-indigo-500 hover:text-indigo-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Assessments
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Assessment Information -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-indigo-500"></i>
                            Assessment Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assessment Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ assessment?.assessment_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assessment Type</h3>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 capitalize">
                            {{ assessment?.assessment_type || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Maximum Score</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ assessment?.max_score || 0 }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Score (Optional)</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ assessment?.score || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assessment Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ assessment?.assessment_date ? new Date(assessment.assessment_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            assessment?.status === 'active'
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                        ]">
                            {{ assessment?.status || 'N/A' }}
                        </span>
                    </div>

                    <div v-if="assessment?.description" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ assessment.description }}</p>
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
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ assessment?.class_model?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Code</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ assessment?.class_model?.code || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ assessment?.class_model?.academic_year || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3">
                        <Link
                            :href="route('admin.classes.show', assessment?.class_model?.id)"
                            class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-eye"></i>
                            View Full Class Details
                        </Link>
                    </div>

                    <!-- Creator Information -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-blue-500"></i>
                            Creator Information
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Created By</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ assessment?.created_by?.full_name || 'N/A' }}</p>
                        <p v-if="assessment?.created_by?.email" class="text-sm text-gray-600 dark:text-gray-400">{{ assessment.created_by.email }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Created At</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ assessment?.created_at ? new Date(assessment.created_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Last Updated</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ assessment?.updated_at ? new Date(assessment.updated_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                        </p>
                    </div>

                    <!-- Grades for this Assessment -->
                    <div v-if="assessment?.grades && assessment.grades.length > 0" class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-graduation-cap text-green-500"></i>
                            Grades for this Assessment ({{ assessment.grades.length }})
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-600">
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Student</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Score</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                        <tr v-for="grade in assessment.grades" :key="grade.id">
                                            <td class="px-4 py-2">
                                                <span class="text-gray-900 dark:text-white">{{ grade.student?.user?.full_name || 'N/A' }}</span>
                                            </td>
                                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">
                                                {{ grade.score || 0 }} / {{ grade.max_score || 0 }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <span :class="[
                                                    'inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold',
                                                    grade.letter_grade === 'A' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                                    grade.letter_grade === 'B' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                                    grade.letter_grade === 'C' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                                    grade.letter_grade === 'D' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                                    'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                                ]">
                                                    {{ grade.letter_grade || 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

