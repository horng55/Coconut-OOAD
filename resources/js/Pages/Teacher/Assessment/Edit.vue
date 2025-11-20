<script setup>
import {router, useForm, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    {label: "Assessments", href: route("teacher.assessments.index")},
    {label: "Edit Assessment", href: "#"}
];

const props = defineProps({
    assessment: Object,
    classes: Array,
});

const form = useForm({
    class_id: props.assessment?.class_id || '',
    assessment_type: props.assessment?.assessment_type || '',
    assessment_name: props.assessment?.assessment_name || '',
    score: props.assessment?.score || '',
    max_score: props.assessment?.max_score || '100',
    assessment_date: props.assessment?.assessment_date ? new Date(props.assessment.assessment_date).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    description: props.assessment?.description || '',
    status: props.assessment?.status || 'active',
});

const submit = () => {
    form.put(route('teacher.assessments.update', props.assessment.id), {
        onSuccess: () => {
            router.visit(route('teacher.assessments.index'));
        }
    });
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-6">
                    <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-edit"></i>
                        Edit Assessment
                    </h1>
                    <p class="text-indigo-100 mt-1">Editing: {{ assessment?.assessment_name || 'Assessment' }}</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.class_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            >
                                <option value="">Select Class</option>
                                <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                    {{ classItem.name }} ({{ classItem.code }})
                                </option>
                            </select>
                            <div v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">{{ form.errors.class_id }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assessment Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.assessment_type"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            >
                                <option value="">Select Type</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                                <option value="exam">Exam</option>
                                <option value="mid_term">Mid-Term</option>
                                <option value="project">Project</option>
                                <option value="participation">Participation</option>
                            </select>
                            <div v-if="form.errors.assessment_type" class="text-red-500 text-sm mt-1">{{ form.errors.assessment_type }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assessment Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.assessment_name"
                                type="text"
                                required
                                placeholder="e.g., Midterm Exam, Chapter 5 Quiz"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            />
                            <div v-if="form.errors.assessment_name" class="text-red-500 text-sm mt-1">{{ form.errors.assessment_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Score (Optional)
                            </label>
                            <input
                                v-model="form.score"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="Default/Example Score"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            />
                            <div v-if="form.errors.score" class="text-red-500 text-sm mt-1">{{ form.errors.score }}</div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Optional: Default or example score</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum Score <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.max_score"
                                type="number"
                                required
                                min="1"
                                step="0.01"
                                placeholder="100"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            />
                            <div v-if="form.errors.max_score" class="text-red-500 text-sm mt-1">{{ form.errors.max_score }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assessment Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.assessment_date"
                                type="date"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            />
                            <div v-if="form.errors.assessment_date" class="text-red-500 text-sm mt-1">{{ form.errors.assessment_date }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Additional description or instructions..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            ></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <Link
                            :href="route('teacher.assessments.index')"
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                            <i v-else class="fas fa-save"></i>
                            {{ form.processing ? 'Updating...' : 'Update Assessment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>

