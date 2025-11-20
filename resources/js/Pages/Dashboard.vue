<script setup>
import App from "../Layouts/App.vue";
import {useGlobalStore} from "../Global/Global.js";
import {computed} from 'vue';
import {Link} from '@inertiajs/vue3';

const title = [{label: "Dashboard", href: route("dashboard.index")}];
const store = useGlobalStore();

// Get current admin info
const currentAdmin = store.currentAdmin;

const props = defineProps({
    stats: Object,
});

// Stats cards with real data from backend
const statsCards = computed(() => [
    {
        icon: 'fas fa-chalkboard-teacher',
        label: 'Teachers',
        value: props.stats?.total_teachers || 0,
        route: 'admin.teachers.index',
        color: 'from-blue-500 to-blue-600',
        bgColor: 'bg-blue-50 dark:bg-blue-900/20',
        iconBg: 'bg-blue-100 dark:bg-blue-900/40',
        iconColor: 'text-blue-600 dark:text-blue-400',
        borderColor: 'border-blue-200 dark:border-blue-800'
    },
    {
        icon: 'fas fa-user-graduate',
        label: 'Students',
        value: props.stats?.total_students || 0,
        route: 'admin.students.index',
        color: 'from-emerald-500 to-emerald-600',
        bgColor: 'bg-emerald-50 dark:bg-emerald-900/20',
        iconBg: 'bg-emerald-100 dark:bg-emerald-900/40',
        iconColor: 'text-emerald-600 dark:text-emerald-400',
        borderColor: 'border-emerald-200 dark:border-emerald-800'
    },
    {
        icon: 'fas fa-book-reader',
        label: 'Classes',
        value: props.stats?.total_classes || 0,
        route: 'admin.classes.index',
        color: 'from-amber-500 to-amber-600',
        bgColor: 'bg-amber-50 dark:bg-amber-900/20',
        iconBg: 'bg-amber-100 dark:bg-amber-900/40',
        iconColor: 'text-amber-600 dark:text-amber-400',
        borderColor: 'border-amber-200 dark:border-amber-800'
    },
    {
        icon: 'fas fa-book',
        label: 'Subjects',
        value: props.stats?.total_subjects || 0,
        route: 'admin.subjects.index',
        color: 'from-green-500 to-green-600',
        bgColor: 'bg-green-50 dark:bg-green-900/20',
        iconBg: 'bg-green-100 dark:bg-green-900/40',
        iconColor: 'text-green-600 dark:text-green-400',
        borderColor: 'border-green-200 dark:border-green-800'
    },
    {
        icon: 'fas fa-tasks',
        label: 'Assignments',
        value: props.stats?.total_assignments || 0,
        route: 'admin.assessments.index',
        color: 'from-purple-500 to-purple-600',
        bgColor: 'bg-purple-50 dark:bg-purple-900/20',
        iconBg: 'bg-purple-100 dark:bg-purple-900/40',
        iconColor: 'text-purple-600 dark:text-purple-400',
        borderColor: 'border-purple-200 dark:border-purple-800'
    },
    {
        icon: 'fas fa-clipboard-check',
        label: 'Submissions',
        value: props.stats?.total_submissions || 0,
        route: 'admin.grades.index',
        color: 'from-indigo-500 to-indigo-600',
        bgColor: 'bg-indigo-50 dark:bg-indigo-900/20',
        iconBg: 'bg-indigo-100 dark:bg-indigo-900/40',
        iconColor: 'text-indigo-600 dark:text-indigo-400',
        borderColor: 'border-indigo-200 dark:border-indigo-800'
    },
    {
        icon: 'fas fa-chart-line',
        label: 'Average Grade',
        value: `${props.stats?.average_grade || 0}%`,
        route: 'admin.grades.index',
        color: 'from-teal-500 to-teal-600',
        bgColor: 'bg-teal-50 dark:bg-teal-900/20',
        iconBg: 'bg-teal-100 dark:bg-teal-900/40',
        iconColor: 'text-teal-600 dark:text-teal-400',
        borderColor: 'border-teal-200 dark:border-teal-800'
    }
]);

const quickActions = [
    { 
        icon: 'fas fa-user-plus', 
        label: 'Add Teacher', 
        route: 'admin.teachers.create', 
        color: 'blue',
        description: 'Register a new teacher'
    },
    { 
        icon: 'fas fa-user-graduate', 
        label: 'Add Student', 
        route: 'admin.students.create', 
        color: 'emerald',
        description: 'Enroll a new student'
    },
    { 
        icon: 'fas fa-book', 
        label: 'Create Class', 
        route: 'admin.classes.create', 
        color: 'amber',
        description: 'Set up a new class'
    },
    { 
        icon: 'fas fa-book-open', 
        label: 'Add Subject', 
        route: 'admin.subjects.create', 
        color: 'indigo',
        description: 'Create a new subject'
    }
];

const getCurrentTime = () => {
    const now = new Date();
    const hours = now.getHours();
    if (hours < 12) return 'Good Morning';
    if (hours < 18) return 'Good Afternoon';
    return 'Good Evening';
};
</script>

<template>
    <App :title="title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            
            <!-- Welcome Section -->
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 40px 40px;"></div>
                </div>
                
                <!-- Gradient Overlay -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-blue-500/10 via-emerald-500/10 to-teal-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-72 h-72 bg-gradient-to-tr from-indigo-500/10 via-purple-500/10 to-pink-500/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 p-8 md:p-10">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-graduation-cap text-white text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ getCurrentTime() }}</p>
                                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                                        {{ currentAdmin?.full_name || 'Admin' }}
                                    </h1>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-lg">
                                Welcome to your School Management Dashboard
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-br from-blue-50 to-emerald-50 dark:from-blue-900/30 dark:to-emerald-900/30 rounded-xl px-5 py-3 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-day text-blue-600 dark:text-blue-400"></i>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <Link 
                    v-for="(stat, index) in statsCards" 
                    :key="index"
                    :href="route(stat.route)"
                    class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:-translate-y-1"
                >
                    <!-- Top Accent Bar -->
                    <div :class="['h-1.5 bg-gradient-to-r', stat.color]"></div>
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div :class="['w-14 h-14 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3', stat.iconBg]">
                                <i :class="[stat.icon, stat.iconColor, 'text-2xl']"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total</p>
                                <p :class="['text-3xl font-bold', stat.iconColor]">
                                    {{ typeof stat.value === 'string' ? stat.value : stat.value.toLocaleString() }}
                                </p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 dark:text-gray-300 font-semibold text-lg">{{ stat.label }}</h3>
                        <div class="mt-3 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">
                            <span>View all</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                    
                    <!-- Hover Effect -->
                    <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-5 transition-opacity duration-300', stat.color]"></div>
                </Link>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-emerald-500 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white">Quick Actions</h2>
                    </div>
                </div>
                
                <div class="p-4 space-y-2">
                    <Link
                        v-for="action in quickActions"
                        :key="action.route"
                        :href="route(action.route)"
                        class="group flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 border border-transparent hover:border-gray-200 dark:hover:border-gray-600"
                    >
                        <div :class="['w-12 h-12 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200', `bg-${action.color}-100 dark:bg-${action.color}-900/30`]">
                            <i :class="[action.icon, `text-${action.color}-600 dark:text-${action.color}-400`]"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ action.label }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ action.description }}</p>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:translate-x-1 transition-all"></i>
                    </Link>
                </div>
            </div>

        </div>
    </App>
</template>

<style scoped>
/* Text gradient support */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
