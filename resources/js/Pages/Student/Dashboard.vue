<script setup>
import {computed} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import StudentLayout from "../../Layouts/StudentLayout.vue";

const page = usePage();
const route = window.route || ((name) => `#${name}`);

const title = [{label: "Dashboard", href: route("student.dashboard")}];

const props = defineProps({
    student: Object,
    enrollments: Array,
    recentGrades: Array,
    assignments: Array,
    stats: Object,
});

const getCurrentTime = () => {
    const now = new Date();
    const hours = now.getHours();
    if (hours < 12) return 'Good Morning';
    if (hours < 18) return 'Good Afternoon';
    return 'Good Evening';
};

const averageGrade = computed(() => {
    if (!props.recentGrades || props.recentGrades.length === 0) return 0;
    const total = props.recentGrades.reduce((sum, grade) => sum + (grade.percentage || 0), 0);
    return Math.round(total / props.recentGrades.length);
});
</script>

<template>
    <StudentLayout :title="title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl shadow-lg p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 mb-2">{{ getCurrentTime() }}</p>
                        <h1 class="text-3xl font-bold mb-1">{{ student?.user?.name || 'Student' }}</h1>
                        <p class="text-emerald-100">Student ID: {{ student?.student_id || 'N/A' }}</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-user-graduate text-6xl opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-emerald-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Enrolled Classes</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats?.total_classes || 0 }}</p>
                        </div>
                        <div class="bg-emerald-100 dark:bg-emerald-900/30 p-3 rounded-lg">
                            <i class="fas fa-book-reader text-emerald-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-amber-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Average Grade</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats?.average_grade || averageGrade }}%</p>
                        </div>
                        <div class="bg-amber-100 dark:bg-amber-900/30 p-3 rounded-lg">
                            <i class="fas fa-chart-line text-amber-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Assignments</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ assignments?.length || 0 }}</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                            <i class="fas fa-tasks text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Classes -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-book-reader text-emerald-500"></i>
                        My Classes
                    </h2>
                </div>
                <div class="p-6">
                    <div v-if="enrollments && enrollments.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="enrollment in enrollments"
                            :key="enrollment.id"
                            class="block p-4 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-gray-700 dark:to-gray-800 rounded-lg border border-emerald-200 dark:border-gray-600"
                        >
                            <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ enrollment.class_model?.name || 'Unknown Class' }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ enrollment.class_model?.grade_level || 'N/A' }}</p>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-chalkboard-teacher text-xs mr-1"></i>
                                    {{ enrollment.class_model?.teachers?.[0]?.user?.name || 'N/A' }}
                                </span>
                                <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded text-xs">
                                    {{ enrollment.status || 'Active' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <i class="fas fa-book-reader text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No classes enrolled yet</p>
                    </div>
                </div>
            </div>

            <!-- Recent Grades -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-star text-amber-500"></i>
                        Recent Grades
                    </h2>
                </div>
                <div class="p-6">
                    <div v-if="recentGrades && recentGrades.length > 0" class="space-y-3">
                        <div 
                            v-for="(grade, index) in recentGrades.slice(0, 5)" 
                            :key="grade?.id || index"
                            v-if="grade?.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ grade.assessment_name || grade.assessment?.assessment_name || 'Assessment' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ grade.class_model?.name || 'N/A' }} • {{ grade.assessment_type }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900 dark:text-white">
                                    {{ grade.percentage ? Math.round(grade.percentage) + '%' : 
                                       (grade.score && grade.max_score ? ((grade.score / grade.max_score) * 100).toFixed(1) + '%' : 'N/A') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ grade.score || 0 }}/{{ grade.max_score || 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <i class="fas fa-chart-line text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No grades available yet</p>
                    </div>
                </div>
            </div>

            <!-- Recent Assignments / Submit Assessment -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-file-upload text-emerald-500"></i>
                        Submit Assessments
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Click Submit to upload your PDF assignments</p>
                </div>
                <div class="p-6">
                    <div v-if="assignments && assignments.length > 0" class="space-y-3">
                        <div 
                            v-for="(assignment, index) in assignments.slice(0, 5)" 
                            :key="assignment?.id || index"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-md">
                                    <i class="fas fa-clipboard-list text-white text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ assignment.assessment_name || 'Untitled' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                        <i class="fas fa-book text-xs"></i>
                                        {{ assignment.class_model?.name || 'N/A' }}
                                        <span class="mx-1">•</span>
                                        <i class="fas fa-calendar text-xs"></i>
                                        {{ assignment.assessment_date ? new Date(assignment.assessment_date).toLocaleDateString() : 'No date' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-semibold capitalize border border-blue-200 dark:border-blue-800">
                                    {{ assignment.assessment_type ? assignment.assessment_type.replace('_', ' ') : 'N/A' }}
                                </span>
                                <Link
                                    :href="`/student/assessments/${assignment.id}`"
                                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg"
                                >
                                    <i class="fas fa-upload"></i>
                                    Submit PDF
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-check text-3xl text-emerald-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">All Caught Up!</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">You have no pending assessments to submit at the moment.</p>
                        <Link
                            :href="'/student/assessments'"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors"
                        >
                            <i class="fas fa-list"></i>
                            View All Assessments
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

