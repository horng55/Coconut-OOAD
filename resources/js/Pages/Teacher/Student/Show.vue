<script setup>
import {Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    {label: "My Students", href: route("teacher.students.index")},
    {label: "Student Details", href: "#"}
];

const props = defineProps({
    student: Object,
    stats: Object,
});
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <Link
                        :href="route('teacher.students.index')"
                        class="text-blue-500 hover:text-blue-600 flex items-center gap-2 mb-4"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Students
                    </Link>
                </div>

                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ student?.user?.full_name || 'N/A' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 font-mono mb-4">
                            Student ID: {{ student?.student_id || 'N/A' }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-400">
                            Email: {{ student?.user?.email || 'N/A' }}
                        </p>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Classes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total_classes || 0 }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Grades</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total_grades || 0 }}</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Attendance</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.total_attendance || 0 }}</p>
                        </div>
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Present</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.present_count || 0 }}</p>
                        </div>
                    </div>

                    <div v-if="student?.enrollments && student.enrollments.length > 0">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Enrolled Classes</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="enrollment in student.enrollments"
                                :key="enrollment.id"
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
                            >
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ enrollment.class_model?.name || 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ enrollment.class_model?.code || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>

