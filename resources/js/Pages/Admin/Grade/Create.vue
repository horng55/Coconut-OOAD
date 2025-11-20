<script setup>
import {ref, watch, computed} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Grades", href: route("admin.grades.index")},
    {label: "Create Grade", href: route("admin.grades.create")}
];

const props = defineProps({
    classes: Array,
    students: Array,
    selectedClass: Object,
    selectedStudent: Object,
    assessments: Array,
});

const form = useForm({
    student_id: props.selectedStudent?.id || '',
    class_id: props.selectedClass?.id || '',
    assessment_id: '',
    score: '',
    comments: '',
});

// Get selected assessment details for display and validation
const selectedAssessment = computed(() => {
    if (form.assessment_id && props.assessments && Array.isArray(props.assessments)) {
        return props.assessments.find(a => a.id == form.assessment_id);
    }
    return null;
});

const availableStudents = ref(props.students || []);

// Watch for props.students changes to update availableStudents
watch(() => props.students, (newStudents) => {
    availableStudents.value = newStudents || [];
}, { immediate: true });

watch(() => form.class_id, (newClassId) => {
    if (newClassId && props.classes) {
        // Reload page with selected class to get students and assessments
        router.get(route('admin.grades.create'), {
            class_id: newClassId,
            student_id: form.student_id
        }, {
            preserveState: true,
            preserveScroll: true
        });
        // Reset assessment selection when class changes
        form.assessment_id = '';
    }
});

const submit = () => {
    form.post(route('admin.grades.store'), {
        onSuccess: () => {
            router.visit(route('admin.grades.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Grade"
            subtitle="Record a new grade for a student"
            icon="fas fa-plus-circle"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-blue-500/20"
        >
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
                            Student <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.student_id"
                            required
                            :disabled="!form.class_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <option value="">Select Student</option>
                            <option v-for="student in (availableStudents || [])" :key="student.id" :value="student.id">
                                {{ student.name }} ({{ student.student_id }})
                            </option>
                        </select>
                        <div v-if="form.errors.student_id" class="text-red-500 text-sm mt-1">{{ form.errors.student_id }}</div>
                        <p v-if="!form.class_id" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Please select a class first</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-clipboard-list text-indigo-500 mr-2"></i>
                            Select Assessment <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.assessment_id"
                            required
                            :disabled="!form.class_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 disabled:opacity-50 disabled:cursor-not-allowed"
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
                        <p v-else-if="form.class_id" class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                            <i class="fas fa-exclamation-circle"></i> No assessments found for this class. Please create an assessment first.
                        </p>
                        <p v-else class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Please select a class first to see available assessments
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
                        {{ form.processing ? 'Creating...' : 'Create Grade' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

