<script setup>
import {Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    {label: "My Classes", href: route("teacher.classes.index")},
    {label: "Class Details", href: "#"}
];

const props = defineProps({
    classItem: Object,
    stats: Object,
    assessments: Array,
});
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <Link
                        :href="route('teacher.classes.index')"
                        class="text-blue-500 hover:text-blue-600 flex items-center gap-2 mb-4"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Classes
                    </Link>
                </div>

                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                    {{ classItem?.name || 'N/A' }}
                                </h1>
                                <p class="text-gray-600 dark:text-gray-400 font-mono">
                                    {{ classItem?.code || 'N/A' }} - {{ classItem?.academic_year || 'N/A' }}
                                </p>
                            </div>
                            <Link
                                :href="route('teacher.assessments.create', { class_id: classItem?.id })"
                                class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center gap-2 font-semibold shadow-lg hover:shadow-xl"
                            >
                                <i class="fas fa-plus"></i>
                                Assign Assessment
                            </Link>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total_students || 0 }}</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Subjects</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total_subjects || 0 }}</p>
                        </div>
                        <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Teachers</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total_teachers || 0 }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">My Assessments</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total_assessments || 0 }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-if="classItem?.subjects && classItem.subjects.length > 0">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Subjects</h2>
                            <div class="space-y-3">
                                <div
                                    v-for="subject in classItem.subjects"
                                    :key="subject.id"
                                    class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
                                >
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ subject.name || 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                        {{ subject.code || 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="classItem?.enrollments && classItem.enrollments.length > 0">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Students</h2>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('teacher.classes.create-student', classItem?.id)"
                                        class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-4 py-2 rounded-lg transition-all text-sm font-semibold flex items-center gap-2"
                                    >
                                        <i class="fas fa-user-plus"></i>
                                        Create New
                                    </Link>
                                    <Link
                                        :href="route('teacher.classes.enroll-student', classItem?.id)"
                                        class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-4 py-2 rounded-lg transition-all text-sm font-semibold flex items-center gap-2"
                                    >
                                        <i class="fas fa-user-check"></i>
                                        Enroll Existing
                                    </Link>
                                </div>
                            </div>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <div
                                    v-for="enrollment in classItem.enrollments"
                                    :key="enrollment.id"
                                    class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
                                >
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ enrollment.student?.user?.full_name || 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ enrollment.student?.student_id || 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else>
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Students</h2>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('teacher.classes.create-student', classItem?.id)"
                                        class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-4 py-2 rounded-lg transition-all text-sm font-semibold flex items-center gap-2"
                                    >
                                        <i class="fas fa-user-plus"></i>
                                        Create New
                                    </Link>
                                    <Link
                                        :href="route('teacher.classes.enroll-student', classItem?.id)"
                                        class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-4 py-2 rounded-lg transition-all text-sm font-semibold flex items-center gap-2"
                                    >
                                        <i class="fas fa-user-check"></i>
                                        Enroll Existing
                                    </Link>
                                </div>
                            </div>
                            <div class="text-center py-8 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <i class="fas fa-users text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                <p class="text-gray-500 dark:text-gray-400">No students enrolled yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Assessments -->
                    <div v-if="assessments && assessments.length > 0">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Assessments</h2>
                            <Link
                                :href="route('teacher.assessments.index', { class_id: classItem?.id })"
                                class="text-blue-500 hover:text-blue-600 text-sm font-medium"
                            >
                                View All
                            </Link>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="assessment in assessments"
                                :key="assessment.id"
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:shadow-md transition-shadow"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ assessment.assessment_name }}
                                    </h3>
                                    <span :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium capitalize',
                                        assessment.assessment_type === 'quiz' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' :
                                        assessment.assessment_type === 'assignment' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' :
                                        assessment.assessment_type === 'exam' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                        assessment.assessment_type === 'project' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
                                    ]">
                                        {{ assessment.assessment_type?.replace('_', ' ') }}
                                    </span>
                                </div>
                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar w-4"></i>
                                        <span>{{ new Date(assessment.assessment_date).toLocaleDateString() }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-star w-4"></i>
                                        <span>Max Score: {{ assessment.max_score }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user-check w-4"></i>
                                        <span>{{ assessment.grades?.length || 0 }} graded</span>
                                    </div>
                                </div>
                                <Link
                                    :href="route('teacher.assessments.show', assessment.id)"
                                    class="mt-3 block w-full text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition-colors text-sm font-medium"
                                >
                                    View Details
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>

