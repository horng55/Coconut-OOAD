<script setup>
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <div class="relative min-h-screen bg-white dark:bg-gray-900 overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-emerald-50 to-teal-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-float-delayed"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-teal-500/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5 pointer-events-none"></div>

        <!-- Main Content -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
            <!-- Header Section -->
            <div class="text-center mb-16">
                <div class="flex justify-center mb-6">
                    <div class="relative w-28 h-28 md:w-32 md:h-32 rounded-3xl overflow-hidden shadow-md ring-4 ring-blue-200/50 dark:ring-blue-800/50 bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center transform hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-graduation-cap text-5xl md:text-6xl text-white"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold bg-gradient-to-r from-blue-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent mb-4 leading-tight">
                    Welcome to Coconut School Management
                </h1>
                <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-3 max-w-2xl mx-auto">
                    Streamlined platform for managing classes, assignments, and grades
                </p>
                <p class="text-base md:text-lg text-gray-500 dark:text-gray-400">
                    Select your portal to get started
                </p>
            </div>

            <!-- Login Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <!-- Admin Login -->
                <Link
                    :href="route('admin.index')"
                    class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-emerald-500/5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-emerald-500 rounded-xl flex items-center justify-center mb-4 shadow-md group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-shield text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Admin Portal</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                            Full system control: manage students, teachers, classes, subjects, assignments, and grades
                        </p>
                        <div class="flex items-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                            <span>Access Portal</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                        </div>
                    </div>
                </Link>

                <!-- Teacher Login -->
                <Link
                    :href="route('teacher.login')"
                    class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-cyan-500/5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mb-4 shadow-md group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chalkboard-teacher text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Teacher Portal</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                            Manage your classes, create and grade assignments for students
                        </p>
                        <div class="flex items-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                            <span>Access Portal</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                        </div>
                    </div>
                </Link>

                <!-- Student Login -->
                <Link
                    :href="route('student.login')"
                    class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center mb-4 shadow-md group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-graduate text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Student Portal</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                            View your classes, assignments, submit work, and check grades
                        </p>
                        <div class="flex items-center text-emerald-600 dark:text-emerald-400 font-semibold text-sm">
                            <span>Access Portal</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Features Section -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-users-cog text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">User Management</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Efficient management of students and teachers
                    </p>
                </div>
                <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-book-reader text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Class Management</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Organize classes, subjects, and manage enrollments efficiently
                    </p>
                </div>
                <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Assignment & Grading</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Create assignments, submit work, and track grades efficiently
                    </p>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="text-center pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400 text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-shield-alt"></i>
                    Secure access for authorized users only
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Floating Animation */
@keyframes float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -30px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-float {
    animation: float 20s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float 25s ease-in-out infinite;
    animation-delay: -5s;
}

/* Grid Pattern */
.bg-grid-pattern {
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
}

/* Text gradient support */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Glassmorphism effect enhancement */
.backdrop-blur-xl {
    -webkit-backdrop-filter: blur(24px);
    backdrop-filter: blur(24px);
}
</style>

