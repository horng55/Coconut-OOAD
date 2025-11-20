<script setup>
import {ref, computed, watch} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Grades", href: route("admin.grades.index")},
    {label: "Edit Grade", href: "#"}
];

const props = defineProps({
    grade: Object,
    assessments: Array,
});

const form = useForm({
    assessment_id: props.grade?.assessment_id || '',
    score: props.grade?.score || '',
    comments: props.grade?.comments || '',
});

// Get selected assessment details for display and validation
const selectedAssessment = computed(() => {
    if (form.assessment_id && props.assessments && Array.isArray(props.assessments)) {
        return props.assessments.find(a => a.id == form.assessment_id);
    }
    return null;
});

const submit = () => {
    form.put(route('admin.grades.update', props.grade.id), {
        onSuccess: () => {
            router.visit(route('admin.grades.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Edit Grade"
            :subtitle="`Editing grade for ${grade?.student?.user?.full_name || 'Student'}`"
            icon="fas fa-edit"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-blue-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.student?.user?.full_name || 'N/A' }}</p>
                        <p v-if="grade?.student?.student_id" class="text-sm text-gray-600 dark:text-gray-400">ID: {{ grade.student.student_id }}</p>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ grade?.class_model?.name || 'N/A' }}</p>
                        <p v-if="grade?.class_model?.code" class="text-sm text-gray-600 dark:text-gray-400">Code: {{ grade.class_model.code }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-clipboard-list text-indigo-500 mr-2"></i>
                            Select Assessment <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.assessment_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        >
                            <option value="">Select Assessment</option>
                            <option v-for="assessment in (assessments || [])" :key="assessment.id" :value="assessment.id">
                                {{ assessment.assessment_name }} ({{ assessment.assessment_type }}) - Max: {{ assessment.max_score }}
                            </option>
                        </select>
                        <div v-if="form.errors.assessment_id" class="text-red-500 text-sm mt-1">{{ form.errors.assessment_id }}</div>
                        <p v-if="assessments && Array.isArray(assessments) && assessments.length > 0" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-info-circle"></i> Select an assessment to link this grade
                        </p>
                        <p v-else class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                            <i class="fas fa-exclamation-circle"></i> No assessments found for this class. Please create an assessment first.
                        </p>
                    </div>

                    <div v-if="selectedAssessment" class="md:col-span-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-800">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                            Assessment Details
                        </h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Type:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white capitalize">{{ selectedAssessment.assessment_type }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ selectedAssessment.assessment_name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Maximum Score:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ selectedAssessment.max_score }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Date:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ new Date(selectedAssessment.assessment_date).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student Score <span class="text-red-500">*</span>
                            <span v-if="selectedAssessment" class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                (Max: {{ selectedAssessment.max_score }})
                            </span>
                        </label>
                        <input
                            v-model="form.score"
                            type="number"
                            required
                            :min="0"
                            :max="selectedAssessment ? selectedAssessment.max_score : undefined"
                            step="0.01"
                            :placeholder="selectedAssessment ? `0 - ${selectedAssessment.max_score}` : '0'"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        />
                        <div v-if="form.errors.score" class="text-red-500 text-sm mt-1">{{ form.errors.score }}</div>
                        <p v-if="selectedAssessment && form.score && parseFloat(form.score) > parseFloat(selectedAssessment.max_score)" class="text-xs text-red-600 dark:text-red-400 mt-1">
                            <i class="fas fa-exclamation-triangle"></i> Score cannot exceed maximum score of {{ selectedAssessment.max_score }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Comments
                        </label>
                        <textarea
                            v-model="form.comments"
                            rows="3"
                            placeholder="Additional comments or notes..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        ></textarea>
                        <div v-if="form.errors.comments" class="text-red-500 text-sm mt-1">{{ form.errors.comments }}</div>
                    </div>
                </div>

                <div v-if="form.score && selectedAssessment" class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Percentage:</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                            {{ ((parseFloat(form.score) / parseFloat(selectedAssessment.max_score)) * 100).toFixed(2) }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Letter Grade:</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                            {{
                                (() => {
                                    const percentage = (parseFloat(form.score) / parseFloat(selectedAssessment.max_score)) * 100;
                                    if (percentage >= 90) return 'A';
                                    if (percentage >= 80) return 'B';
                                    if (percentage >= 70) return 'C';
                                    if (percentage >= 60) return 'D';
                                    return 'F';
                                })()
                            }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.grades.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Updating...' : 'Update Grade' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

