<script setup>
import { useForm } from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    { label: "Assignments", href: route("teacher.assignments.index") },
    { label: "Create Assignment", href: "#" }
];

const props = defineProps({
    classes: Array,
    preselectedClassId: [String, Number],
});

const form = useForm({
    class_id: props.preselectedClassId || "",
    title: "",
    description: "",
    instructions: "",
    due_date: "",
    due_time: "23:59",
    max_score: 100,
    attachment: null,
    status: "published",
});

const handleFileChange = (event) => {
    form.attachment = event.target.files[0];
};

const submit = () => {
    form.post(route("teacher.assignments.store"));
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        Create New Assignment
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Create a new assignment for your class</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.class_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        >
                            <option value="">Select a class</option>
                            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                                {{ cls.name }} ({{ cls.code }}) - {{ cls.academic_year }}
                            </option>
                        </select>
                        <div v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">{{ form.errors.class_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assignment Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="e.g., Chapter 5 Homework, Essay on Climate Change"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        />
                        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Brief description of the assignment"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Instructions
                        </label>
                        <textarea
                            v-model="form.instructions"
                            rows="6"
                            placeholder="Detailed instructions for completing the assignment"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        ></textarea>
                        <div v-if="form.errors.instructions" class="text-red-500 text-sm mt-1">{{ form.errors.instructions }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.due_date"
                                type="date"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                            />
                            <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Due Time
                            </label>
                            <input
                                v-model="form.due_time"
                                type="time"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                            />
                            <div v-if="form.errors.due_time" class="text-red-500 text-sm mt-1">{{ form.errors.due_time }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Max Score <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.max_score"
                                type="number"
                                required
                                min="0"
                                step="0.01"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                            />
                            <div v-if="form.errors.max_score" class="text-red-500 text-sm mt-1">{{ form.errors.max_score }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Attachment (Optional)
                        </label>
                        <input
                            type="file"
                            @change="handleFileChange"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum file size: 10MB</p>
                        <div v-if="form.errors.attachment" class="text-red-500 text-sm mt-1">{{ form.errors.attachment }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        >
                            <option value="draft">Draft (not visible to students)</option>
                            <option value="published">Published (visible to students)</option>
                            <option value="closed">Closed (no longer accepting submissions)</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div class="flex items-center gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <i class="fas fa-save"></i>
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Assignment</span>
                        </button>
                        <a
                            :href="route('teacher.assignments.index')"
                            class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>
