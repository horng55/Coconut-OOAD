<script setup>
import {computed, ref} from "vue";
import {Link} from "@inertiajs/vue3";
import TeacherLayout from "../../Layouts/TeacherLayout.vue";

const title = [{label: "Dashboard", href: route("teacher.dashboard")}];

const props = defineProps({
    teacher: Object,
    classes: Array,
    recentGrades: Array,
    stats: Object,
});

const getCurrentTime = () => {
    const now = new Date();
    const hours = now.getHours();
    if (hours < 12) return 'Good Morning';
    if (hours < 18) return 'Good Afternoon';
    return 'Good Evening';
};

const getGreetingIcon = () => {
    const hours = new Date().getHours();
    if (hours < 12) return 'fa-sun';
    if (hours < 18) return 'fa-cloud-sun';
    return 'fa-moon';
};

const getLetterGradeColor = (percentage) => {
    if (percentage >= 90) return 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400';
    if (percentage >= 80) return 'text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400';
    if (percentage >= 70) return 'text-amber-600 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400';
    if (percentage >= 60) return 'text-orange-600 bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400';
    return 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400';
};

const getLetterGrade = (percentage) => {
    if (percentage >= 90) return 'A';
    if (percentage >= 80) return 'B';
    if (percentage >= 70) return 'C';
    if (percentage >= 60) return 'D';
    return 'F';
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            <!-- Hero Welcome Section with Glassmorphism -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-blue-600 to-cyan-500 rounded-3xl shadow-2xl">
                <!-- Animated Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                </div>
                
                <div class="relative p-8 md:p-10">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <i :class="['fas', getGreetingIcon(), 'text-3xl', 'text-yellow-200', 'animate-bounce']"></i>
                                <p class="text-blue-100 text-lg font-medium">{{ getCurrentTime() }}</p>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 tracking-tight">
                                {{ teacher?.user?.full_name || 'Teacher' }}
                            </h1>
                            <div class="flex items-center gap-4 flex-wrap">
                                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                    <i class="fas fa-id-badge text-white/90 text-sm"></i>
                                    <span class="text-white/90 text-sm font-medium">{{ teacher?.employee_id || 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                    <i class="fas fa-envelope text-white/90 text-sm"></i>
                                    <span class="text-white/90 text-sm font-medium">{{ teacher?.user?.email || 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="flex flex-col gap-3">
                            <Link :href="route('teacher.grades.create')" 
                                class="flex items-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i class="fas fa-plus-circle"></i>
                                <span>Add Grade</span>
                            </Link>
                            <Link :href="route('teacher.attendances.create')" 
                                class="flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white border-2 border-white/30 px-6 py-3 rounded-xl font-semibold hover:bg-white/20 transition-all">
                                <i class="fas fa-clipboard-check"></i>
                                <span>Take Attendance</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards with Hover Effects -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Classes Card -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all">
                                <i class="fas fa-chalkboard text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ stats?.total_classes || 0 }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Total Classes</p>
                        <div class="mt-3 flex items-center text-sm text-blue-600 dark:text-blue-400">
                            <i class="fas fa-arrow-right mr-2"></i>
                            <Link :href="route('teacher.classes.index')" class="hover:underline">View all classes</Link>
                        </div>
                    </div>
                </div>

                <!-- Total Students Card -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all">
                                <i class="fas fa-users text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ stats?.total_students || 0 }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Total Students</p>
                        <div class="mt-3 flex items-center text-sm text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-arrow-right mr-2"></i>
                            <Link :href="route('teacher.students.index')" class="hover:underline">View all students</Link>
                        </div>
                    </div>
                </div>

                <!-- Assignments Card -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-amber-600/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all">
                                <i class="fas fa-tasks text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ stats?.total_assignments || 0 }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Assignments</p>
                        <div class="mt-3 flex items-center text-sm text-amber-600 dark:text-amber-400">
                            <i class="fas fa-arrow-right mr-2"></i>
                            <Link :href="route('teacher.assessments.index')" class="hover:underline">Manage assignments</Link>
                        </div>
                    </div>
                </div>

                <!-- Grades Card -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-purple-600/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all">
                                <i class="fas fa-clipboard-check text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ stats?.total_grades || 0 }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Total Grades</p>
                        <div class="mt-3 flex items-center text-sm text-purple-600 dark:text-purple-400">
                            <i class="fas fa-arrow-right mr-2"></i>
                            <Link :href="route('teacher.grades.index')" class="hover:underline">View all grades</Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- My Classes - Takes 2 columns -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-800 p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-chalkboard text-white"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Classes</h2>
                                </div>
                                <Link :href="route('teacher.classes.index')" 
                                    class="flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 bg-white dark:bg-gray-700 px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
                                    <span>View All</span>
                                    <i class="fas fa-arrow-right"></i>
                                </Link>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="classes && classes.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <Link
                                    v-for="classItem in classes.slice(0, 4)"
                                    :key="classItem.id"
                                    :href="route('teacher.classes.show', classItem.id)"
                                    class="group relative block p-5 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-xl transition-all transform hover:-translate-y-1"
                                >
                                    <div class="absolute top-3 right-3">
                                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-arrow-right text-white text-xs"></i>
                                        </div>
                                    </div>
                                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 pr-10">{{ classItem.name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ classItem.code || 'N/A' }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xs"></i>
                                            </div>
                                            <span class="font-semibold">{{ classItem.enrollments?.length || 0 }}</span>
                                        </div>
                                        <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold">
                                            Active
                                        </span>
                                    </div>
                                </Link>
                            </div>
                            <div v-else class="text-center py-16">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-chalkboard text-4xl text-gray-300 dark:text-gray-600"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No classes assigned yet</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Classes will appear here once assigned</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Grades - Takes 1 column -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-gray-700 dark:to-gray-800 p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-star text-white"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Recent Grades</h2>
                                </div>
                            </div>
                            <Link :href="route('teacher.grades.index')" 
                                class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">
                                <span>View All</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </Link>
                        </div>
                        <div class="p-6 max-h-96 overflow-y-auto">
                            <div v-if="recentGrades && recentGrades.length > 0" class="space-y-3">
                                <div 
                                    v-for="grade in recentGrades.slice(0, 5)" 
                                    :key="grade.id"
                                    class="group p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl hover:shadow-md transition-all border border-gray-200 dark:border-gray-600"
                                >
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                                <i class="fas fa-user-graduate text-white"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ grade.student?.user?.full_name || grade.student?.user?.name || 'Unknown' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ grade.class_model?.name || 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div :class="['px-3 py-2 rounded-xl font-bold text-sm flex-shrink-0', getLetterGradeColor(grade.percentage)]">
                                            {{ getLetterGrade(grade.percentage) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Score</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ grade.score }}/{{ grade.max_score }} ({{ grade.percentage }}%)</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-16">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-chart-line text-4xl text-gray-300 dark:text-gray-600"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No grades yet</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Start grading assignments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>

