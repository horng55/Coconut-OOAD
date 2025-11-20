<script setup>
import {Link} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";

const title = [
    {label: "My Classes", href: route("student.classes.index")},
    {label: "Class Details", href: "#"}
];

const props = defineProps({
    enrollment: Object,
});
</script>

<template>
    <StudentLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <Link
                        :href="route('student.classes.index')"
                        class="text-emerald-500 hover:text-emerald-600 flex items-center gap-2 mb-4"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Classes
                    </Link>
                </div>

                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ enrollment?.class_model?.name || 'N/A' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 font-mono mb-4">
                            {{ enrollment?.class_model?.code || 'N/A' }} - {{ enrollment?.class_model?.academic_year || 'N/A' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-if="enrollment?.class_model?.teachers && enrollment.class_model.teachers.length > 0">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Teachers</h2>
                            <div class="space-y-3">
                                <div
                                    v-for="teacher in enrollment.class_model.teachers"
                                    :key="teacher.id"
                                    class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
                                >
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ teacher.user?.full_name || 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ teacher.user?.email || 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="enrollment?.class_model?.subjects && enrollment.class_model.subjects.length > 0">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Subjects</h2>
                            <div class="space-y-3">
                                <div
                                    v-for="subject in enrollment.class_model.subjects"
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
                    </div>

                    <div v-if="enrollment?.class_model?.enrollments && enrollment.class_model.enrollments.length > 0">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Classmates</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="enroll in enrollment.class_model.enrollments"
                                :key="enroll.id"
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
                            >
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ enroll.student?.user?.full_name || 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ enroll.student?.student_id || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

