<script setup>
import { useForm } from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    { label: "Classes", href: route("teacher.classes.index") },
    { label: "Create Class", href: "#" }
];

const props = defineProps({
    subjects: Array,
});

const form = useForm({
    name: "",
    code: "",
    description: "",
    academic_year: new Date().getFullYear() + "/" + (new Date().getFullYear() + 1),
    max_students: 30,
    subject_ids: [],
});

const submit = () => {
    form.post(route("teacher.classes.store"));
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        Create New Class
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Create a new class and assign subjects</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="e.g., Grade 10A, Math 101"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.code"
                                type="text"
                                required
                                placeholder="e.g., CLS-001"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.code" class="text-red-500 text-sm mt-1">{{ form.errors.code }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.academic_year"
                                type="text"
                                required
                                placeholder="e.g., 2024/2025"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.academic_year" class="text-red-500 text-sm mt-1">{{ form.errors.academic_year }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Max Students <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.max_students"
                                type="number"
                                required
                                min="1"
                                max="100"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.max_students" class="text-red-500 text-sm mt-1">{{ form.errors.max_students }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Class description..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subjects
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label
                                v-for="subject in subjects"
                                :key="subject.id"
                                class="flex items-center gap-2 p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            >
                                <input
                                    v-model="form.subject_ids"
                                    type="checkbox"
                                    :value="subject.id"
                                    class="w-4 h-4 text-blue-500 rounded focus:ring-2 focus:ring-blue-500/50"
                                />
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ subject.name }}</span>
                            </label>
                        </div>
                        <div v-if="form.errors.subject_ids" class="text-red-500 text-sm mt-1">{{ form.errors.subject_ids }}</div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Class</span>
                        </button>

                        <a
                            :href="route('teacher.classes.index')"
                            class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg font-semibold transition-colors"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>
