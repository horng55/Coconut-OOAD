<script setup>
import {computed} from "vue";
import {Link} from "@inertiajs/vue3";
import ParentLayout from "../../Layouts/ParentLayout.vue";

const title = [{label: "Dashboard", href: route("parent.dashboard")}];

const props = defineProps({
    parent: Object,
    children: Array,
    enrollments: Array,
    recentGrades: Array,
    recentAttendance: Array,
    announcements: Array,
    feePayments: Array,
    pendingPayments: Array,
    stats: Object,
});

const getCurrentTime = () => {
    const now = new Date();
    const hours = now.getHours();
    if (hours < 12) return 'Good Morning';
    if (hours < 18) return 'Good Afternoon';
    return 'Good Evening';
};

const attendancePercentage = computed(() => {
    if (props.stats?.total_attendance === 0) return 0;
    return Math.round((props.stats?.present_count / props.stats?.total_attendance) * 100);
});

const averageGrade = computed(() => {
    if (!props.recentGrades || props.recentGrades.length === 0) return 0;
    const total = props.recentGrades.reduce((sum, grade) => sum + (grade.percentage || 0), 0);
    return Math.round(total / props.recentGrades.length);
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount || 0);
};
</script>

<template>
    <ParentLayout :title="title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <!-- Welcome Section -->
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 40px 40px;"></div>
                </div>
                
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-pink-500/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 p-8 md:p-10">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-users text-white text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ getCurrentTime() }}</p>
                                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                                        {{ parent?.user?.full_name || 'Parent' }}
                                    </h1>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Relationship: {{ parent?.relationship || 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-lg">
                                Welcome to your Parent Portal - Monitor your children's academic progress
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-xl px-5 py-3 border border-indigo-200 dark:border-indigo-800">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-day text-indigo-600 dark:text-indigo-400"></i>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                                <i class="fas fa-child text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total</p>
                                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ stats?.total_children || 0 }}
                                </p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Children</h3>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-purple-500 to-purple-600"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                                <i class="fas fa-book-reader text-purple-600 dark:text-purple-400 text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total</p>
                                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ stats?.total_classes || 0 }}
                                </p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Enrolled Classes</h3>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-600 dark:text-blue-400 text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Average</p>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ averageGrade }}%
                                </p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Average Grade</h3>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-green-500 to-green-600"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                <i class="fas fa-percentage text-green-600 dark:text-green-400 text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Rate</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                    {{ attendancePercentage }}%
                                </p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Attendance Rate</h3>
                    </div>
                </div>

                <!-- Fee Payments Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-yellow-500 to-amber-500"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pending</p>
                                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ stats?.pending_payments_count || 0 }}
                                </p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 dark:text-gray-300 font-semibold text-lg">Pending Payments</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ formatCurrency(stats?.total_pending || 0) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pending Payments Alert -->
            <div v-if="pendingPayments && pendingPayments.length > 0" class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-l-4 border-red-500 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-red-900 dark:text-red-300 mb-2">
                            Pending Fee Payments
                        </h3>
                        <div class="space-y-2">
                            <div
                                v-for="payment in pendingPayments"
                                :key="payment.id"
                                class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded"
                            >
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ payment.fee?.name || 'N/A' }} - {{ payment.student?.user?.full_name || 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Due: {{ payment.due_date ? new Date(payment.due_date).toLocaleDateString() : 'N/A' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-red-600 dark:text-red-400">
                                        {{ formatCurrency(payment.remaining_amount) }}
                                    </p>
                                    <span :class="[
                                        'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                        payment.status === 'overdue' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                        payment.status === 'partial' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                        'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                    ]">
                                        {{ payment.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Children Section -->
            <div v-if="children && children.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <i class="fas fa-child text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white">My Children</h2>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="child in children"
                            :key="child.id"
                            class="p-4 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 hover:shadow-md transition-shadow"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-indigo-900 dark:text-indigo-300 text-lg mb-1">
                                        {{ child.user?.full_name || 'N/A' }}
                                    </h3>
                                    <p class="text-sm text-indigo-700 dark:text-indigo-400">
                                        ID: {{ child.student_id || 'N/A' }}
                                    </p>
                                </div>
                                <span :class="[
                                    'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium',
                                    child.status === 'active' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="child.status === 'active' ? 'fas fa-check' : 'fas fa-pause'"></i>
                                    {{ child.status }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <p class="text-indigo-600 dark:text-indigo-400">Classes</p>
                                    <p class="font-semibold text-indigo-900 dark:text-indigo-300">
                                        {{ child.enrollments?.length || 0 }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-indigo-600 dark:text-indigo-400">Grades</p>
                                    <p class="font-semibold text-indigo-900 dark:text-indigo-300">
                                        {{ child.grades?.length || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Enrolled Classes -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-reader text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white">Children's Classes</h2>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="enrollments && enrollments.length > 0" class="space-y-4">
                            <div
                                v-for="enrollment in enrollments"
                                :key="enrollment.id"
                                class="p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 hover:shadow-md transition-shadow"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="font-semibold text-purple-900 dark:text-purple-300 text-lg">
                                                {{ enrollment.class_model?.name || 'N/A' }}
                                            </h3>
                                            <span class="text-xs text-purple-600 dark:text-purple-400">
                                                ({{ enrollment.student?.user?.full_name || 'N/A' }})
                                            </span>
                                        </div>
                                        <p class="text-sm text-purple-700 dark:text-purple-400 mb-2">
                                            Code: {{ enrollment.class_model?.code || 'N/A' }}
                                        </p>
                                        <div class="flex items-center gap-4 text-sm text-purple-600 dark:text-purple-400">
                                            <span v-if="enrollment.class_model?.teachers && enrollment.class_model.teachers.length > 0">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                {{ enrollment.class_model.teachers.map(t => t.user?.full_name).filter(Boolean).join(', ') || 'N/A' }}
                                            </span>
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ enrollment.class_model?.academic_year || 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span :class="[
                                        'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium',
                                        enrollment.status === 'active' 
                                            ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                    ]">
                                        <i :class="enrollment.status === 'active' ? 'fas fa-check' : 'fas fa-pause'"></i>
                                        {{ enrollment.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <i class="fas fa-book-reader text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">No classes enrolled yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Announcements -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                <i class="fas fa-bullhorn text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white">Announcements</h2>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div v-if="announcements && announcements.length > 0" class="space-y-3">
                            <div
                                v-for="announcement in announcements"
                                :key="announcement.id"
                                class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800"
                            >
                                <h4 class="font-semibold text-blue-900 dark:text-blue-300 text-sm mb-1">
                                    {{ announcement.title }}
                                </h4>
                                <p class="text-xs text-blue-700 dark:text-blue-400 line-clamp-2 mb-2">
                                    {{ announcement.content }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-blue-600 dark:text-blue-500">
                                    <span>{{ announcement.class_model?.name || 'General' }}</span>
                                    <span>{{ announcement.created_at ? new Date(announcement.created_at).toLocaleDateString() : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6">
                            <i class="fas fa-bullhorn text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No announcements</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Grades and Attendance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Grades -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white">Recent Grades</h2>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="recentGrades && recentGrades.length > 0" class="space-y-3">
                            <div
                                v-for="grade in recentGrades"
                                :key="grade.id"
                                class="p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800"
                            >
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-amber-900 dark:text-amber-300">
                                                {{ grade.assessment_name }}
                                            </h4>
                                            <span class="text-xs text-amber-600 dark:text-amber-400">
                                                ({{ grade.student?.user?.full_name || 'N/A' }})
                                            </span>
                                        </div>
                                        <p class="text-sm text-amber-700 dark:text-amber-400">
                                            {{ grade.class_model?.name || 'N/A' }}
                                        </p>
                                    </div>
                                    <span :class="[
                                        'inline-flex items-center px-2 py-1 rounded text-xs font-bold',
                                        grade.letter_grade === 'A' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                        grade.letter_grade === 'B' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                        grade.letter_grade === 'C' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                        grade.letter_grade === 'D' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                        'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                    ]">
                                        {{ grade.letter_grade }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-amber-600 dark:text-amber-400">
                                        {{ grade.score }}/{{ grade.max_score }} ({{ grade.percentage }}%)
                                    </span>
                                    <span class="text-amber-500 dark:text-amber-400">
                                        {{ grade.assessment_date ? new Date(grade.assessment_date).toLocaleDateString() : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6">
                            <i class="fas fa-chart-line text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No grades available</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white">Recent Attendance</h2>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="recentAttendance && recentAttendance.length > 0" class="space-y-3">
                            <div
                                v-for="attendance in recentAttendance"
                                :key="attendance.id"
                                class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800"
                            >
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-green-900 dark:text-green-300">
                                                {{ attendance.class_model?.name || 'N/A' }}
                                            </h4>
                                            <span class="text-xs text-green-600 dark:text-green-400">
                                                ({{ attendance.student?.user?.full_name || 'N/A' }})
                                            </span>
                                        </div>
                                        <p class="text-sm text-green-700 dark:text-green-400">
                                            {{ attendance.date ? new Date(attendance.date).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' }) : 'N/A' }}
                                        </p>
                                    </div>
                                    <span :class="[
                                        'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium',
                                        attendance.status === 'present' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                        attendance.status === 'late' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                        attendance.status === 'excused' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                        'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                    ]">
                                        <i :class="
                                            attendance.status === 'present' ? 'fas fa-check' :
                                            attendance.status === 'late' ? 'fas fa-clock' :
                                            attendance.status === 'excused' ? 'fas fa-info' :
                                            'fas fa-times'
                                        "></i>
                                        {{ attendance.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6">
                            <i class="fas fa-clipboard-check text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No attendance records</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Payments Section -->
            <div v-if="feePayments && feePayments.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-amber-500 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white">Recent Fee Payments</h2>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            v-for="payment in feePayments"
                            :key="payment.id"
                            class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800"
                        >
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-semibold text-yellow-900 dark:text-yellow-300">
                                            {{ payment.fee?.name || 'N/A' }}
                                        </h4>
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400">
                                            ({{ payment.student?.user?.full_name || 'N/A' }})
                                        </span>
                                    </div>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                        Due: {{ payment.due_date ? new Date(payment.due_date).toLocaleDateString() : 'N/A' }}
                                    </p>
                                </div>
                                <span :class="[
                                    'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium',
                                    payment.status === 'paid' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                    payment.status === 'partial' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300' :
                                    payment.status === 'overdue' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                    'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="
                                        payment.status === 'paid' ? 'fas fa-check-circle' :
                                        payment.status === 'partial' ? 'fas fa-exclamation-circle' :
                                        payment.status === 'overdue' ? 'fas fa-times-circle' :
                                        'fas fa-clock'
                                    "></i>
                                    {{ payment.status }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="text-yellow-600 dark:text-yellow-400">Amount: </span>
                                    <span class="font-semibold text-yellow-900 dark:text-yellow-300">{{ formatCurrency(payment.amount) }}</span>
                                </div>
                                <div>
                                    <span class="text-yellow-600 dark:text-yellow-400">Paid: </span>
                                    <span class="font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(payment.amount_paid) }}</span>
                                </div>
                                <div>
                                    <span class="text-yellow-600 dark:text-yellow-400">Remaining: </span>
                                    <span class="font-semibold text-red-600 dark:text-red-400">{{ formatCurrency(payment.remaining_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ParentLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

