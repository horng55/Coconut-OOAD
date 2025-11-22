<script setup>
import {ref} from "vue";
import {useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import {AlertError, AlertSuccess} from "../../../Utils/AlertUtil.js";

const title = [
    {label: "Assessments", href: route("admin.assessments.index")},
    {label: "Create", href: route("admin.assessments.create")}
];

const props = defineProps({
    classes: Array,
});

const form = useForm({
    class_id: '',
    assessment_name: '',
    assessment_type: 'exam',
    assessment_date: '',
    max_score: '',
    description: '',
    status: 'active',
});

const isBusy = ref(false);

const submit = () => {
    isBusy.value = true;
    form.post(route('admin.assessments.store'), {
        onSuccess: () => {
            isBusy.value = false;
            AlertSuccess({message: 'Assessment created successfully!'});
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            AlertError({message: firstError || 'Failed to create assessment'});
            isBusy.value = false;
        },
        onFinish: () => {
            isBusy.value = false;
        },
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create Assessment"
            subtitle="Add a new assessment to the system"
            icon="fas fa-plus-circle"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-purple-500/20"
        >
            <template #actions>
                <Link :href="route('admin.assessments.index')" 
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </Link>
            </template>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Class Selection -->
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-school mr-2 text-indigo-500"></i>Class<span class="text-red-500">*</span>
                        </label>
                        <select 
                            v-model="form.class_id" 
                            id="class_id"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            required
                        >
                            <option value="">Select a class</option>
                            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                                {{ cls.name }} ({{ cls.code }})
                            </option>
                        </select>
                        <p v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.class_id }}
                        </p>
                    </div>

                    <!-- Assessment Name -->
                    <div>
                        <label for="assessment_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-2 text-blue-500"></i>Assessment Name<span class="text-red-500">*</span>
                        </label>
                        <input 
                            v-model="form.assessment_name" 
                            type="text" 
                            id="assessment_name"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="e.g., Midterm Exam, Final Project"
                            required
                        />
                        <p v-if="form.errors.assessment_name" class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.assessment_name }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Assessment Type -->
                        <div>
                            <label for="assessment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-purple-500"></i>Type<span class="text-red-500">*</span>
                            </label>
                            <select 
                                v-model="form.assessment_type" 
                                id="assessment_type"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                required
                            >
                                <option value="exam">Exam</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                                <option value="project">Project</option>
                                <option value="presentation">Presentation</option>
                            </select>
                            <p v-if="form.errors.assessment_type" class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.assessment_type }}
                            </p>
                        </div>

                        <!-- Max Score -->
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-star mr-2 text-yellow-500"></i>Max Score<span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="form.max_score" 
                                type="number" 
                                id="max_score"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all"
                                placeholder="100"
                                required
                            />
                            <p v-if="form.errors.max_score" class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.max_score }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Assessment Date -->
                        <div>
                            <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2 text-green-500"></i>Assessment Date<span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="form.assessment_date" 
                                type="date" 
                                id="assessment_date"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                required
                            />
                            <p v-if="form.errors.assessment_date" class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.assessment_date }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-toggle-on mr-2 text-teal-500"></i>Status<span class="text-red-500">*</span>
                            </label>
                            <select 
                                v-model="form.status" 
                                id="status"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"
                                required
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <p v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.status }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-gray-500"></i>Description (Optional)
                        </label>
                        <textarea 
                            v-model="form.description" 
                            id="description"
                            rows="4"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all resize-none"
                            placeholder="Enter assessment description, instructions, or any additional notes..."
                        ></textarea>
                        <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link :href="route('admin.assessments.index')" 
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </Link>
                        <button 
                            type="submit"
                            :disabled="isBusy"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 shadow-lg hover:shadow-indigo-500/50 transform hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                        >
                            <span v-if="!isBusy" class="flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                Create Assessment
                            </span>
                            <span v-else class="flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i>
                                Creating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </AdminPageWrapper>
    </App>
</template>
