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
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ classItem?.name || 'N/A' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 font-mono mb-4">
                            {{ classItem?.code || 'N/A' }} - {{ classItem?.academic_year || 'N/A' }}
                        </p>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Students</h2>
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
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>

