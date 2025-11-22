<script setup>
import { useForm } from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";
import { Link } from "@inertiajs/vue3";

const title = [
    { label: "Assignments", href: route("student.assignments.index") },
    { label: "Assignment Details", href: "#" }
];

const props = defineProps({
    assignment: Object,
    submission: Object,
});

const form = useForm({
    comments: "",
    file: null,
});

const handleFileChange = (event) => {
    form.file = event.target.files[0];
};

const submit = () => {
    form.post(route("student.assignments.submit", props.assignment.id));
};

const downloadAttachment = (assignmentId) => {
    window.location.href = route('student.assignments.download-attachment', assignmentId);
};

const isOverdue = () => {
    const now = new Date();
    const dueDate = new Date(props.assignment.due_date + ' ' + (props.assignment.due_time || '23:59:59'));
    return now > dueDate;
};

const canSubmit = () => {
    return !props.submission && props.assignment.status === 'published';
};
</script>

<template>
    <StudentLayout :title="title">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Assignment Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ assignment.title }}
                            </h1>
                            <span v-if="isOverdue() && !submission" class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                                Overdue
                            </span>
                            <span v-if="submission" :class="['px-3 py-1 text-white text-xs font-semibold rounded-full', submission.status === 'graded' ? 'bg-green-500' : 'bg-blue-500']">
                                {{ submission.status === 'graded' ? 'Graded' : 'Submitted' }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-1">
                            {{ assignment.class_model?.name }} ({{ assignment.class_model?.code }})
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Teacher: {{ assignment.teacher?.user?.name }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Due Date</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ new Date(assignment.due_date).toLocaleDateString() }}
                        </p>
                        <p v-if="assignment.due_time" class="text-sm text-gray-600 dark:text-gray-400">
                            at {{ assignment.due_time }}
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Max Score</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ assignment.max_score }} points
                        </p>
                    </div>
                    <div v-if="submission?.score !== null" class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Your Score</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ submission.score }} / {{ assignment.max_score }}
                        </p>
                    </div>
                </div>

                <div v-if="assignment.description" class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ assignment.description }}</p>
                </div>

                <div v-if="assignment.instructions" class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Instructions</h3>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ assignment.instructions }}</p>
                </div>

                <div v-if="assignment.attachment_path" class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Assignment Attachment</h3>
                    <button
                        @click="downloadAttachment(assignment.id)"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2"
                    >
                        <i class="fas fa-download"></i>
                        Download Attachment
                    </button>
                </div>
            </div>

            <!-- Submission Status -->
            <div v-if="submission" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Your Submission</h2>

                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Submitted on</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ new Date(submission.submitted_at).toLocaleString() }}
                            </p>
                        </div>
                        <span :class="['px-3 py-1 text-white text-xs font-semibold rounded-full', submission.status === 'graded' ? 'bg-green-500' : 'bg-blue-500']">
                            {{ submission.status }}
                        </span>
                    </div>

                    <div v-if="submission.comments" class="mb-4">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Your Comments:</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ submission.comments }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Submitted File:</p>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <i class="fas fa-file"></i>
                            {{ submission.file_name }}
                            <span class="text-gray-500">
                                ({{ (submission.file_size / 1024).toFixed(2) }} KB)
                            </span>
                        </div>
                    </div>

                    <div v-if="submission.status === 'graded'" class="p-4 bg-green-50 dark:bg-gray-900 rounded-lg">
                        <div class="mb-3">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Score:</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ submission.score }} / {{ assignment.max_score }} points
                            </p>
                        </div>
                        <div v-if="submission.teacher_feedback">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Teacher Feedback:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ submission.teacher_feedback }}</p>
                        </div>
                        <p v-if="submission.graded_at" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Graded on {{ new Date(submission.graded_at).toLocaleString() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submission Form -->
            <div v-if="canSubmit()" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Submit Your Work</h2>

                <div v-if="isOverdue()" class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-1"></i>
                        <div>
                            <p class="font-semibold text-yellow-800 dark:text-yellow-300">This assignment is overdue</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">You can still submit, but it will be marked as late.</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Comments (Optional)
                        </label>
                        <textarea
                            v-model="form.comments"
                            rows="4"
                            placeholder="Add any comments or notes about your submission"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        ></textarea>
                        <div v-if="form.errors.comments" class="text-red-500 text-sm mt-1">{{ form.errors.comments }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Upload File <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="file"
                            required
                            @change="handleFileChange"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum file size: 10MB</p>
                        <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</div>
                    </div>

                    <div class="flex items-center gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <i class="fas fa-upload"></i>
                            <span v-if="form.processing">Submitting...</span>
                            <span v-else>Submit Assignment</span>
                        </button>
                        <Link
                            :href="route('student.assignments.index')"
                            class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-arrow-left"></i>
                            Back to Assignments
                        </Link>
                    </div>
                </form>
            </div>

            <div v-else-if="!submission" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="text-center py-8">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Assignment Not Available</p>
                    <p class="text-gray-500 dark:text-gray-400">This assignment is currently closed for submissions.</p>
                    <Link
                        :href="route('student.assignments.index')"
                        class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-semibold transition-all duration-200"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Assignments
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
