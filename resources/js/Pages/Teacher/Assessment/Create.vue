<script setup>
import {ref, watch} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    {label: "Assessments", href: "#"},
    {label: "Create Assessment", href: route("teacher.assessments.create")}
];

const props = defineProps({
    classes: Array,
    selectedClass: Object,
    students: Array,
});

const form = useForm({
    class_id: props.selectedClass?.id || '',
    assessment_type: '',
    assessment_name: '',
    score: '',
    max_score: '100',
    assessment_date: new Date().toISOString().split('T')[0],
    description: '',
    student_ids: [],
});

watch(() => form.class_id, (newClassId) => {
    if (newClassId) {
        router.get(route('teacher.assessments.create'), {
            class_id: newClassId
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
});

const submit = () => {
    console.log('Submit function called');
    console.log('Form data:', form.data());
    console.log('Form errors before submit:', form.errors);
    
    form.post(route('teacher.assessments.store'), {
        onSuccess: () => {
            console.log('Assessment created successfully');
            // Reset form after successful creation
            form.reset();
            form.class_id = props.selectedClass?.id || '';
            form.max_score = '100';
            form.assessment_date = new Date().toISOString().split('T')[0];
        },
        onError: (errors) => {
            console.error('Validation errors received:', errors);
            console.error('Full error object:', JSON.stringify(errors, null, 2));
            // Display errors to user
            Object.keys(errors).forEach(key => {
                console.error(`Error for ${key}:`, errors[key]);
            });
        },
        onFinish: () => {
            console.log('Form submission finished');
            console.log('Form errors after submit:', form.errors);
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
                        <i class="fas fa-plus-circle"></i>
                        Create New Assessment
                    </h1>
                    <p class="text-indigo-100 mt-1">Create a new assessment for a class</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Error Summary -->
                    <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl mt-1"></i>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-red-800 dark:text-red-300 mb-2">Validation Errors</h3>
                                <ul class="list-disc list-inside space-y-1">
                                    <li v-for="(error, field) in form.errors" :key="field" class="text-sm text-red-700 dark:text-red-400">
                                        <strong>{{ field }}:</strong> {{ error }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

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

                        <div v-if="students && students.length > 0" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user-graduate text-indigo-500 mr-2"></i>
                                Assign to Students (Optional)
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                <label
                                    v-for="student in students"
                                    :key="student.id"
                                    class="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-white dark:hover:bg-gray-800 transition-colors"
                                    :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700': form.student_ids.includes(student.id) }"
                                >
                                    <input
                                        v-model="form.student_ids"
                                        type="checkbox"
                                        :value="student.id"
                                        class="mt-1 w-4 h-4 text-indigo-500 rounded focus:ring-2 focus:ring-indigo-500/50"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ student.name }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ student.student_id }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            {{ student.email }}
                                        </p>
                                    </div>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <i class="fas fa-info-circle"></i> Select students to assign this assessment (creates grade entries for grading)
                            </p>
                            <div v-if="form.errors.student_ids" class="text-red-500 text-sm mt-1">{{ form.errors.student_ids }}</div>
                        </div>

                        <div v-else-if="form.class_id" class="md:col-span-2">
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                                <p class="text-yellow-800 dark:text-yellow-300 text-sm flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    No students enrolled in this class yet. You can still create the assessment and assign students later.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                            <i v-else class="fas fa-save"></i>
                            {{ form.processing ? 'Creating...' : 'Create Assessment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>

