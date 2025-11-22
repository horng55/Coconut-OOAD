<script setup>
import { useForm } from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import { Link } from "@inertiajs/vue3";

const title = [
    { label: "Assignments", href: route("teacher.assignments.index") },
    { label: "Assignment Details", href: "#" }
];

const props = defineProps({
    assignment: Object,
    totalStudents: Number,
    submittedCount: Number,
    gradedCount: Number,
});

const gradeForm = useForm({
    score: '',
    teacher_feedback: '',
});

const selectedSubmission = ref(null);

const openGradeModal = (submission) => {
    selectedSubmission.value = submission;
    gradeForm.score = submission.score || '';
    gradeForm.teacher_feedback = submission.teacher_feedback || '';
};

const submitGrade = () => {
    if (!selectedSubmission.value) return;
    
    gradeForm.post(
        route('teacher.assignments.grade-submission', {
            assignment: props.assignment.id,
            submission: selectedSubmission.value.id
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedSubmission.value = null;
                gradeForm.reset();
            },
        }
    );
};

const getStatusColor = (status) => {
    const colors = {
        'submitted': 'bg-blue-500',
        'graded': 'bg-green-500',
    };
    return colors[status] || 'bg-gray-500';
};

const downloadSubmission = (assignmentId, submissionId) => {
    window.location.href = route('teacher.assignments.download-submission', {
        assignment: assignmentId,
        submission: submissionId
    });
};

const downloadAttachment = (assignmentId) => {
    window.location.href = route('teacher.assignments.download-attachment', assignmentId);
};

const isOverdue = () => {
    const now = new Date();
    const dueDate = new Date(props.assignment.due_date + ' ' + (props.assignment.due_time || '23:59:59'));
    return now > dueDate;
};
</script>

<script>
import { ref } from 'vue';
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Assignment Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ assignment.title }}
                            </h1>
                            <span :class="['px-3 py-1 text-white text-xs font-semibold rounded-full', assignment.status === 'published' ? 'bg-green-500' : assignment.status === 'draft' ? 'bg-gray-500' : 'bg-red-500']">
                                {{ assignment.status }}
                            </span>
                            <span v-if="isOverdue() && assignment.status === 'published'" class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                                Overdue
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ assignment.class_model?.name }} ({{ assignment.class_model?.code }})
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('teacher.assignments.edit', assignment.id)"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2"
                        >
                            <i class="fas fa-edit"></i>
                            Edit
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
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
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Submitted</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ submittedCount }} / {{ totalStudents }}
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Graded</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ gradedCount }} / {{ submittedCount }}
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Attachment</h3>
                    <button
                        @click="downloadAttachment(assignment.id)"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2"
                    >
                        <i class="fas fa-download"></i>
                        Download Attachment
                    </button>
                </div>
            </div>

            <!-- Submissions List -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Student Submissions</h2>

                <div v-if="assignment.submissions && assignment.submissions.length > 0" class="space-y-4">
                    <div
                        v-for="submission in assignment.submissions"
                        :key="submission.id"
                        class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                    {{ submission.student?.user?.name || 'Unknown Student' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    Submitted: {{ new Date(submission.submitted_at).toLocaleString() }}
                                </p>
                                <span :class="['px-3 py-1 text-white text-xs font-semibold rounded-full', getStatusColor(submission.status)]">
                                    {{ submission.status }}
                                </span>
                            </div>
                            <div v-if="submission.score !== null" class="text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Score</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ submission.score }} / {{ assignment.max_score }}
                                </p>
                            </div>
                        </div>

                        <div v-if="submission.comments" class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Student Comments:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ submission.comments }}</p>
                        </div>

                        <div v-if="submission.teacher_feedback" class="mb-4 p-4 bg-yellow-50 dark:bg-gray-900 rounded-lg">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Your Feedback:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ submission.teacher_feedback }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                @click="downloadSubmission(assignment.id, submission.id)"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <i class="fas fa-download"></i>
                                Download ({{ submission.file_name }})
                            </button>
                            <button
                                @click="openGradeModal(submission)"
                                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <i class="fas fa-star"></i>
                                {{ submission.status === 'graded' ? 'Update Grade' : 'Grade Submission' }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No Submissions Yet</p>
                    <p class="text-gray-500 dark:text-gray-400">Students haven't submitted their work yet.</p>
                </div>
            </div>
        </div>

        <!-- Grade Modal -->
        <div v-if="selectedSubmission" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="selectedSubmission = null">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    Grade Submission - {{ selectedSubmission.student?.user?.name }}
                </h3>
                <form @submit.prevent="submitGrade" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Score (out of {{ assignment.max_score }}) <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="gradeForm.score"
                            type="number"
                            required
                            min="0"
                            :max="assignment.max_score"
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="gradeForm.errors.score" class="text-red-500 text-sm mt-1">{{ gradeForm.errors.score }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Feedback
                        </label>
                        <textarea
                            v-model="gradeForm.teacher_feedback"
                            rows="4"
                            placeholder="Provide feedback to the student"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        ></textarea>
                        <div v-if="gradeForm.errors.teacher_feedback" class="text-red-500 text-sm mt-1">{{ gradeForm.errors.teacher_feedback }}</div>
                    </div>
                    <div class="flex items-center gap-3 pt-4">
                        <button
                            type="submit"
                            :disabled="gradeForm.processing"
                            class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors disabled:opacity-50"
                        >
                            {{ gradeForm.processing ? 'Saving...' : 'Submit Grade' }}
                        </button>
                        <button
                            type="button"
                            @click="selectedSubmission = null"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>
