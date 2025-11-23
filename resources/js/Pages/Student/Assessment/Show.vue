<script setup>
import {Link, useForm} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";

const route = window.route || ((name) => `#${name}`);

const props = defineProps({
    assessment: Object,
    myGrade: Object,
    mySubmission: Object,
});

const title = [
    {label: "Assessments", href: "/student/assessments"},
    {label: "Details", href: `/student/assessments/${props.assessment.id}`}
];

const form = useForm({
    comments: "",
    file: null,
});

const handleFileChange = (event) => {
    form.file = event.target.files[0];
};

const submitAssessment = () => {
    form.post(`/student/assessments/${props.assessment.id}/submit`);
};

const downloadSubmission = () => {
    window.location.href = `/student/assessments/${props.assessment.id}/download-submission`;
};

const getTypeColor = (type) => {
    const colors = {
        quiz: 'from-blue-500/20 to-cyan-500/20 text-blue-500',
        assignment: 'from-purple-500/20 to-pink-500/20 text-purple-500',
        exam: 'from-red-500/20 to-orange-500/20 text-red-500',
        mid_term: 'from-orange-500/20 to-yellow-500/20 text-orange-500',
        project: 'from-green-500/20 to-emerald-500/20 text-green-500',
        participation: 'from-yellow-500/20 to-amber-500/20 text-yellow-500',
    };
    return colors[type] || 'from-gray-500/20 to-slate-500/20 text-gray-500';
};

const getGradeColor = (percentage) => {
    if (percentage >= 80) return 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30';
    if (percentage >= 60) return 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30';
    if (percentage >= 40) return 'text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30';
    return 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30';
};
</script>

<template>
    <StudentLayout :title="title">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        href="/student/assessments"
                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Assessment Details</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ assessment.class_model?.name }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Assessment Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div :class="['p-6 bg-gradient-to-br', getTypeColor(assessment.assessment_type)]">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ assessment.assessment_name }}</h2>
                                    <span class="text-sm opacity-90 capitalize">{{ assessment.assessment_type?.replace('_', ' ') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Description</h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    {{ assessment.description || 'No description provided.' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Assessment Date</div>
                                    <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-calendar text-purple-500"></i>
                                        <span class="font-medium">{{ new Date(assessment.assessment_date).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Maximum Score</div>
                                    <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-star text-purple-500"></i>
                                        <span class="font-medium">{{ assessment.max_score }} points</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Class</div>
                                    <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-chalkboard text-purple-500"></i>
                                        <span class="font-medium">{{ assessment.class_model?.name }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Created By</div>
                                    <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-user text-purple-500"></i>
                                        <span class="font-medium">{{ assessment.created_by?.full_name || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grade Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="fas fa-award text-purple-500"></i>
                                Your Grade
                            </h3>
                        </div>

                        <div v-if="myGrade" class="p-6">
                            <div class="text-center mb-6">
                                <div :class="['inline-flex items-center justify-center w-32 h-32 rounded-full text-4xl font-bold mb-4', getGradeColor((myGrade.score / assessment.max_score) * 100)]">
                                    {{ ((myGrade.score / assessment.max_score) * 100).toFixed(1) }}%
                                </div>
                                <div class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
                                    {{ myGrade.score }} / {{ assessment.max_score }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    points earned
                                </div>
                            </div>

                            <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div v-if="myGrade.feedback" class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Teacher's Feedback</div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ myGrade.feedback }}</p>
                                </div>

                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex justify-between py-2">
                                        <span>Graded by:</span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ myGrade.graded_by?.full_name || 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span>Graded on:</span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ myGrade.updated_at ? new Date(myGrade.updated_at).toLocaleDateString() : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="p-6 text-center">
                            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clock text-3xl text-gray-400"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Not Graded Yet</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Your teacher hasn't graded this assessment yet. Please check back later.
                            </p>
                        </div>
                    </div>

                    <!-- Submission Status Card -->
                    <div v-if="mySubmission" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6">
                        <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                Your Submission
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-green-800 dark:text-green-300">Submitted Successfully</p>
                                        <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                                            {{ new Date(mySubmission.submitted_at).toLocaleString() }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="mySubmission.comments" class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Your Comments:</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                                    {{ mySubmission.comments }}
                                </p>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Submitted File:</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-file text-purple-500"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ mySubmission.file_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ (mySubmission.file_size / 1024).toFixed(2) }} KB
                                            </p>
                                        </div>
                                    </div>
                                    <button
                                        @click="downloadSubmission"
                                        class="px-3 py-1 bg-purple-500 hover:bg-purple-600 text-white text-sm rounded-lg transition-colors flex items-center gap-2"
                                    >
                                        <i class="fas fa-download"></i>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Form -->
                    <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6">
                        <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <i class="fas fa-upload text-purple-500"></i>
                                Submit Your Work
                            </h3>
                        </div>

                        <div class="p-6">
                            <form @submit.prevent="submitAssessment" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Comments (Optional)
                                    </label>
                                    <textarea
                                        v-model="form.comments"
                                        rows="4"
                                        placeholder="Add any comments about your submission"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                                    ></textarea>
                                    <div v-if="form.errors.comments" class="text-red-500 text-sm mt-1">{{ form.errors.comments }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Upload PDF File <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="file"
                                            accept=".pdf,application/pdf"
                                            required
                                            @change="handleFileChange"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                                        />
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <i class="fas fa-info-circle"></i> PDF files only, maximum size: 10MB
                                    </p>
                                    <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</div>
                                </div>

                                <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    >
                                        <i class="fas fa-upload"></i>
                                        <span v-if="form.processing">Submitting...</span>
                                        <span v-else>Submit Assessment</span>
                                    </button>
                                    <Link
                                        href="/student/assessments"
                                        class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-all duration-200 flex items-center gap-2"
                                    >
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
