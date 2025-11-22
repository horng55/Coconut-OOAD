<script setup>
import {computed} from "vue";
import {Link} from "@inertiajs/vue3";
import StudentLayout from "../../Layouts/StudentLayout.vue";

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
                            v-for="grade in recentGrades.slice(0, 5)" 
                            :key="grade.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ grade.assessment?.title || 'Assessment' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ grade.class_model?.name || 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900 dark:text-white">{{ grade.percentage }}%</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ grade.score }}/{{ grade.max_score }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <i class="fas fa-chart-line text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No grades available yet</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Assignments -->
            <div v-if="assignments && assignments.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-tasks text-blue-500"></i>
                        Recent Assignments
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div 
                            v-for="assignment in assignments.slice(0, 5)" 
                            :key="assignment.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ assignment.title }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Due: {{ new Date(assignment.due_date).toLocaleDateString() }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg text-sm">
                                {{ assignment.max_score }} pts
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

