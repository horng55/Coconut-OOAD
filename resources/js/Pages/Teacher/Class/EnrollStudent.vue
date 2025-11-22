<script setup>
import { useForm } from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    { label: "Classes", href: route("teacher.classes.index") },
    { label: "Enroll Student", href: "#" }
];

const props = defineProps({
    classItem: Object,
    availableStudents: Array,
});

const form = useForm({
    student_id: "",
});

const submit = () => {
    form.post(route("teacher.classes.store-enrollment", props.classItem.id));
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-check text-white text-xl"></i>
                        </div>
                        Enroll Existing Student
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">
                        Enroll an existing student in {{ classItem?.name }}
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div v-if="availableStudents && availableStudents.length > 0">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Student <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.student_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option value="">-- Select a student --</option>
                            <option
                                v-for="student in availableStudents"
                                :key="student.id"
                                :value="student.id"
                            >
                                {{ student.name }} ({{ student.student_id }}) - {{ student.email }}
                            </option>
                        </select>
                        <div v-if="form.errors.student_id" class="text-red-500 text-sm mt-1">{{ form.errors.student_id }}</div>
                    </div>

                    <div v-else class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 border border-yellow-200 dark:border-yellow-800 text-center">
                        <i class="fas fa-exclamation-triangle text-4xl text-yellow-600 dark:text-yellow-400 mb-3"></i>
                        <p class="text-yellow-800 dark:text-yellow-300 font-semibold mb-2">
                            No Available Students
                        </p>
                        <p class="text-yellow-700 dark:text-yellow-400 text-sm">
                            All active students are already enrolled in this class.
                        </p>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button
                            v-if="availableStudents && availableStudents.length > 0"
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Enrolling...</span>
                            <span v-else>Enroll Student</span>
                        </button>

                        <a
                            :href="route('teacher.classes.show', classItem?.id)"
                            class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg font-semibold transition-colors"
                        >
                            Back to Class
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>
