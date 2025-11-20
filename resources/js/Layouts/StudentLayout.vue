<script setup>
import {useDarkMode} from "../Composables/darkMode.js";
import {router, usePage, Link} from "@inertiajs/vue3";
import {computed, ref, onMounted, onUnmounted, watch, nextTick} from "vue";
import QRCode from "qrcode";

const {isDark, toggleDarkMode} = useDarkMode();
const page = usePage();
const showProfileMenu = ref(false);
const showIdCardModal = ref(false);
const qrCodeDataUrl = ref(null);

const props = defineProps({
    title: Array,
    idCard: Object,
});

const currentStudent = computed(() => {
    try {
        const studentData = page.props.current_student;
        if (!studentData) return null;
        if (typeof studentData === 'string') {
            return JSON.parse(studentData);
        }
        return studentData;
    } catch {
        return null;
    }
});

const logout = () => {
    if (confirm('Are you sure you want to logout?')) {
        router.post(route('student.logout'));
    }
};

const toggleProfileMenu = () => {
    showProfileMenu.value = !showProfileMenu.value;
};

const toggleIdCardModal = () => {
    showIdCardModal.value = !showIdCardModal.value;
    if (showIdCardModal.value && props.idCard?.card_number) {
        generateQRCode();
    }
};

// Generate QR code from card number
const generateQRCode = async () => {
    if (!props.idCard?.card_number) return;
    
    try {
        const dataUrl = await QRCode.toDataURL(props.idCard.card_number, {
            width: 200,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        });
        qrCodeDataUrl.value = dataUrl;
    } catch (error) {
        console.error('Error generating QR code:', error);
    }
};

// Watch for modal opening to generate QR code
watch(() => showIdCardModal.value, (isOpen) => {
    if (isOpen && props.idCard?.card_number) {
        nextTick(() => {
            generateQRCode();
        });
    }
});

// Close profile menu when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.profile-menu-container')) {
        showProfileMenu.value = false;
    }
};

// Close ID card modal on Escape key
const handleEscapeKey = (event) => {
    if (event.key === 'Escape' && showIdCardModal.value) {
        showIdCardModal.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleEscapeKey);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscapeKey);
});
</script>

<template>
    <div :class="{ 'dark': isDark }">
        <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900 transition-all duration-300">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-md border-b border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-graduate text-emerald-500"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                            Student Portal
                        </h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- ID Card Button -->
                        <button 
                            v-if="idCard"
                            @click="toggleIdCardModal"
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 dark:hover:bg-purple-800 text-purple-600 dark:text-purple-400 transition-all duration-200 relative"
                            title="View ID Card"
                        >
                            <i class="fas fa-id-card text-sm"></i>
                            <span v-if="idCard.status === 'active'" class="absolute top-0 right-0 w-2 h-2 bg-green-500 rounded-full"></span>
                        </button>

                        <!-- Dark Mode Toggle -->
                        <button 
                            @click="toggleDarkMode"
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-all duration-200"
                            title="Toggle Dark Mode"
                        >
                            <i v-if="isDark" class="fas fa-moon text-sm"></i>
                            <i v-else class="fas fa-sun text-sm"></i>
                        </button>

                        <!-- Profile Menu -->
                        <div class="relative profile-menu-container">
                            <button
                                @click="toggleProfileMenu"
                                class="h-10 flex items-center gap-2 px-3 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200"
                            >
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-user-graduate text-white text-xs"></i>
                                </div>
                                <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-300">{{ currentStudent?.user?.full_name || "Student" }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400"></i>
                            </button>

                            <!-- Profile Dropdown Menu -->
                            <div 
                                v-if="showProfileMenu"
                                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
                            >
                                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ currentStudent?.user?.full_name || "Student" }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ currentStudent?.user?.email || "" }}</p>
                                </div>
                                <div class="p-2">
                                    <button
                                        @click="logout"
                                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Navigation Menu -->
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="px-6 py-3">
                    <div class="flex items-center gap-4 flex-wrap">
                        <Link
                            :href="route('student.dashboard')"
                            :class="[
                                'px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2',
                                $page.url.startsWith('/student/dashboard')
                                    ? 'bg-emerald-500 text-white shadow-lg'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]"
                        >
                            <i class="fas fa-home"></i>
                            Dashboard
                        </Link>
                        <Link
                            :href="route('student.classes.index')"
                            :class="[
                                'px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2',
                                $page.url.startsWith('/student/classes')
                                    ? 'bg-emerald-500 text-white shadow-lg'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]"
                        >
                            <i class="fas fa-book"></i>
                            Classes
                        </Link>
                        <Link
                            :href="route('student.grades.index')"
                            :class="[
                                'px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2',
                                $page.url.startsWith('/student/grades')
                                    ? 'bg-emerald-500 text-white shadow-lg'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]"
                        >
                            <i class="fas fa-graduation-cap"></i>
                            Grades
                        </Link>
                        <Link
                            :href="route('student.attendances.index')"
                            :class="[
                                'px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2',
                                $page.url.startsWith('/student/attendances')
                                    ? 'bg-emerald-500 text-white shadow-lg'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]"
                        >
                            <i class="fas fa-clipboard-check"></i>
                            Attendance
                        </Link>
                        <Link
                            :href="route('student.timetable.index')"
                            :class="[
                                'px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2',
                                $page.url.startsWith('/student/timetable')
                                    ? 'bg-emerald-500 text-white shadow-lg'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]"
                        >
                            <i class="fas fa-calendar-alt"></i>
                            Timetable
                        </Link>
                        <Link
                            :href="route('student.announcements.index')"
                            :class="[
                                'px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2',
                                $page.url.startsWith('/student/announcements')
                                    ? 'bg-emerald-500 text-white shadow-lg'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]"
                        >
                            <i class="fas fa-bullhorn"></i>
                            Announcements
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="p-6">
                <slot/>
            </div>

            <!-- ID Card Modal -->
            <div 
                v-if="showIdCardModal && idCard"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                @click.self="showIdCardModal = false"
            >
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <i class="fas fa-id-card text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Student ID Card</h3>
                                    <p class="text-sm text-white/80">{{ idCard.card_number }}</p>
                                </div>
                            </div>
                            <button
                                @click="showIdCardModal = false"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 transition-colors"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ID Card Preview -->
                    <div class="p-6">
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border-2 border-purple-200 dark:border-purple-800">
                            <!-- Card Header -->
                            <div class="text-center mb-6">
                                <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-100 mb-2">SCHOOL MANAGEMENT</h2>
                                <p class="text-sm text-purple-700 dark:text-purple-300">STUDENT IDENTIFICATION CARD</p>
                            </div>

                            <!-- Card Content -->
                            <div class="flex gap-4">
                                <!-- Photo -->
                                <div class="flex-shrink-0">
                                    <div class="w-24 h-24 bg-white dark:bg-gray-700 rounded-lg border-2 border-purple-300 dark:border-purple-600 overflow-hidden flex items-center justify-center">
                                        <img 
                                            v-if="idCard.photo_path" 
                                            :src="`/storage/${idCard.photo_path}`" 
                                            alt="Student Photo"
                                            class="w-full h-full object-cover"
                                        >
                                        <i v-else class="fas fa-user text-4xl text-gray-400"></i>
                                    </div>
                                </div>

                                <!-- Student Info -->
                                <div class="flex-1 space-y-2">
                                    <div>
                                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">NAME</p>
                                        <p class="text-lg font-bold text-purple-900 dark:text-purple-100">{{ currentStudent?.user?.full_name || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">STUDENT ID</p>
                                        <p class="text-base font-semibold text-purple-900 dark:text-purple-100">{{ idCard?.student?.student_id || currentStudent?.student_id || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">CARD NUMBER</p>
                                        <p class="text-base font-mono font-semibold text-purple-900 dark:text-purple-100">{{ idCard.card_number }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="mt-6 pt-4 border-t border-purple-200 dark:border-purple-700">
                                <div class="flex justify-between text-xs text-purple-600 dark:text-purple-400">
                                    <div>
                                        <p class="font-medium">ISSUE DATE</p>
                                        <p>{{ idCard.issue_date ? new Date(idCard.issue_date).toLocaleDateString() : 'N/A' }}</p>
                                    </div>
                                    <div v-if="idCard.expiry_date">
                                        <p class="font-medium">EXPIRY DATE</p>
                                        <p>{{ new Date(idCard.expiry_date).toLocaleDateString() }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mt-4 flex justify-center">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium',
                                    idCard.status === 'active' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                ]">
                                    <i :class="idCard.status === 'active' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
                                    {{ idCard.status ? idCard.status.charAt(0).toUpperCase() + idCard.status.slice(1) : 'N/A' }}
                                </span>
                            </div>

                            <!-- QR Code Section -->
                            <div class="mt-4 pt-4 border-t border-purple-200 dark:border-purple-700">
                                <div class="flex items-center justify-center">
                                    <div class="bg-white dark:bg-gray-700 p-2 rounded-lg border-2 border-purple-300 dark:border-purple-600 inline-block">
                                        <img 
                                            v-if="qrCodeDataUrl" 
                                            :src="qrCodeDataUrl" 
                                            alt="QR Code"
                                            class="w-24 h-24"
                                        >
                                        <div v-else class="w-24 h-24 flex items-center justify-center">
                                            <i class="fas fa-spinner fa-spin text-purple-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

