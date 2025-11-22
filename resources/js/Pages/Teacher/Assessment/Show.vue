<script setup>
import {ref, computed, onMounted} from "vue";
import {Link, useForm} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const props = defineProps({
    assessment: Object,
    students: Array,
    highlightSubmission: Number,
});

const title = [
    {label: "Assessments", href: route("teacher.assessments.index")},
    {label: props.assessment.assessment_name, href: route("teacher.assessments.show", props.assessment.id)}
];

const editingGrade = ref(null);
const gradeForm = useForm({
    score: 0,
    feedback: "",
});

const startEditGrade = (student) => {
    editingGrade.value = student.id;
    gradeForm.score = student.score || 0;
    gradeForm.feedback = student.feedback || "";
};

const cancelEdit = () => {
    editingGrade.value = null;
    gradeForm.reset();
};

const saveGrade = (student) => {
    gradeForm.post(route("teacher.assessments.update-grade", student.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingGrade.value = null;
            gradeForm.reset();
        },
    });
};

const downloadSubmission = (submissionId) => {
    window.location.href = route('teacher.assessments.download-submission', submissionId);
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

const getGradeColor = (score) => {
    const percentage = (score / props.assessment.max_score) * 100;
    if (percentage >= 80) return 'text-green-600 dark:text-green-400';
    if (percentage >= 60) return 'text-blue-600 dark:text-blue-400';
    if (percentage >= 40) return 'text-orange-600 dark:text-orange-400';
    return 'text-red-600 dark:text-red-400';
};

const submittedCount = computed(() => {
    return props.students.filter(s => s.has_submission).length;
});

const gradedCount = computed(() => {
    return props.students.filter(s => s.score > 0 || s.feedback).length;
});

// Auto-open grading form if coming from dashboard with a submission
onMounted(() => {
    if (props.highlightSubmission) {
        const student = props.students.find(s => s.submission?.id === props.highlightSubmission);
        if (student) {
            startEditGrade(student);
            // Scroll to the student card
            setTimeout(() => {
                const element = document.getElementById(`student-${student.id}`);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        }
    }
});
</script>

<template>
    <TeacherLayout :title="title">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('teacher.assessments.index')"
                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Assessment Details & Grading</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ assessment.class_model?.name }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Action Alert - When coming from submission link -->
            <div v-if="highlightSubmission && students && students.length > 0" class="mb-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border-2 border-indigo-200 dark:border-indigo-800 rounded-xl p-4 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-hand-point-down text-white text-xl animate-bounce"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Ready to Grade!
                        </h3>
                        <p class="text-sm text-indigo-700 dark:text-indigo-400 mb-3">
                            A student submission is waiting for your review. Scroll down to the <strong>"Student Submissions & Grading"</strong> section below to grade this student.
                        </p>
                        <button
                            @click="() => {
                                const student = students.find(s => s.submission?.id === highlightSubmission);
                                if (student) {
                                    const element = document.getElementById(`student-${student.id}`);
                                    if (element) {
                                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        if (editingGrade !== student.id) {
                                            startEditGrade(student);
                                        }
                                    }
                                }
                            }"
                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all flex items-center gap-2 font-semibold shadow-md hover:shadow-lg transform hover:scale-105"
                        >
                            <i class="fas fa-arrow-down"></i>
                            Jump to Student & Open Grading Form
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Assessment Info -->
                <div class="lg:col-span-1">
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

                            <div class="grid grid-cols-1 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Assessment Date</div>
                                    <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-calendar text-indigo-500"></i>
                                        <span class="font-medium">{{ new Date(assessment.assessment_date).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Maximum Score</div>
                                    <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-star text-indigo-500"></i>
                                        <span class="font-medium">{{ assessment.max_score }} points</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Class</div>
                                    <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-chalkboard text-indigo-500"></i>
                                        <span class="font-medium">{{ assessment.class_model?.name }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Status</div>
                                    <span :class="[
                                        'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                        assessment.status === 'active'
                                            ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                            : 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                    ]">
                                        {{ assessment.status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Statistics</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Total Students:</span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ students.length }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Submitted:</span>
                                        <span class="font-semibold text-blue-600 dark:text-blue-400">{{ submittedCount }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Graded:</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">{{ gradedCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students List with Grading -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-6">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <i class="fas fa-users"></i>
                                Student Submissions & Grading
                            </h3>
                            <p class="text-indigo-100 text-sm mt-1">View submissions and grade students</p>
                        </div>

                        <div class="p-6">
                            <!-- Debug Info & Help Message -->
                            <div v-if="!students || students.length === 0" class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-graduate text-4xl text-gray-400"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">No Students Assigned</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    This assessment doesn't have any students assigned yet. Students need to be assigned when creating the assessment.
                                </p>
                                <Link 
                                    :href="route('teacher.assessments.index')"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-colors"
                                >
                                    <i class="fas fa-arrow-left"></i>
                                    Back to Assessments
                                </Link>
                            </div>
                            
                            <div v-else class="space-y-4">
                                <div
                                    v-for="student in students"
                                    :key="student.id"
                                    :id="`student-${student.id}`"
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow"
                                >
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                                                {{ student.student_name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                ID: {{ student.student_number }} • {{ student.student_email }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div :class="['text-2xl font-bold', getGradeColor(student.score)]">
                                                {{ student.score }} / {{ assessment.max_score }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ ((student.score / assessment.max_score) * 100).toFixed(1) }}%
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submission Info -->
                                    <div v-if="student.has_submission" class="mb-3 bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border-2 border-green-200 dark:border-green-800">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-start gap-3 flex-1">
                                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-check-circle text-white text-lg"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold text-green-800 dark:text-green-300 mb-1">
                                                        <i class="fas fa-file-pdf text-red-500 mr-1"></i>
                                                        PDF Submitted
                                                    </p>
                                                    <p class="text-xs text-green-700 dark:text-green-400 mb-2">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        {{ new Date(student.submission.submitted_at).toLocaleString() }}
                                                    </p>
                                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-2 mb-2">
                                                        <p class="text-xs text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                                            <i class="fas fa-file text-indigo-500"></i>
                                                            <span class="font-medium">{{ student.submission.file_name }}</span>
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            <i class="fas fa-hdd mr-1"></i>
                                                            Size: {{ (student.submission.file_size / 1024).toFixed(2) }} KB
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-start gap-1">
                                                            <i class="fas fa-folder-open mt-0.5 flex-shrink-0"></i>
                                                            <span class="break-all">Storage: {{ student.submission.file_path }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button
                                                @click="downloadSubmission(student.submission.id)"
                                                class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm rounded-lg transition-colors flex items-center gap-2 shadow-md hover:shadow-lg flex-shrink-0"
                                            >
                                                <i class="fas fa-download"></i>
                                                Download
                                            </button>
                                        </div>
                                        <div v-if="student.submission.comments" class="pt-3 border-t border-green-200 dark:border-green-800">
                                            <p class="text-xs font-semibold text-green-800 dark:text-green-300 mb-1">
                                                <i class="fas fa-comment-dots mr-1"></i>
                                                Student Comments:
                                            </p>
                                            <p class="text-xs text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-lg p-2 italic">
                                                "{{ student.submission.comments }}"
                                            </p>
                                        </div>
                                    </div>
                                    <div v-else class="mb-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border-2 border-yellow-200 dark:border-yellow-800">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                                            <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">No submission yet</p>
                                        </div>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1 ml-6">Waiting for student to upload PDF</p>
                                    </div>

                                    <!-- Grading Section -->
                                    <div v-if="editingGrade === student.id" class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-900/50 dark:to-gray-800/50 rounded-lg p-5 border-2 border-indigo-200 dark:border-indigo-800">
                                        <div class="flex items-center gap-2 mb-4">
                                            <i class="fas fa-pen-to-square text-indigo-600 dark:text-indigo-400 text-lg"></i>
                                            <h5 class="text-lg font-bold text-indigo-900 dark:text-indigo-300">Grade Student</h5>
                                        </div>
                                        <form @submit.prevent="saveGrade(student)" class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                    Score (Max: {{ assessment.max_score }}) <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model="gradeForm.score"
                                                    type="number"
                                                    step="0.01"
                                                    :max="assessment.max_score"
                                                    min="0"
                                                    required
                                                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-lg font-semibold"
                                                    placeholder="Enter score"
                                                />
                                                <div v-if="gradeForm.errors.score" class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    {{ gradeForm.errors.score }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                    Feedback (Optional)
                                                </label>
                                                <textarea
                                                    v-model="gradeForm.feedback"
                                                    rows="4"
                                                    placeholder="Add feedback for the student..."
                                                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                                ></textarea>
                                                <div v-if="gradeForm.errors.feedback" class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    {{ gradeForm.errors.feedback }}
                                                </div>
                                            </div>
                                            <div class="flex gap-3 pt-2">
                                                <button
                                                    type="submit"
                                                    :disabled="gradeForm.processing"
                                                    class="flex-1 px-5 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02]"
                                                >
                                                    <i class="fas fa-check-circle text-lg"></i>
                                                    <span v-if="gradeForm.processing">Saving...</span>
                                                    <span v-else>Save Grade</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="cancelEdit"
                                                    class="px-5 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-all flex items-center gap-2 font-semibold"
                                                >
                                                    <i class="fas fa-times"></i>
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div v-else class="space-y-3">
                                        <div v-if="student.feedback || student.graded_by" class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div v-if="student.feedback" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 mb-2">
                                                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-1">
                                                        <i class="fas fa-comment-dots mr-1"></i>
                                                        Teacher Feedback:
                                                    </p>
                                                    <p class="text-sm text-blue-700 dark:text-blue-400">{{ student.feedback }}</p>
                                                </div>
                                                <div v-if="student.graded_by" class="text-xs text-gray-500 dark:text-gray-400">
                                                    <i class="fas fa-user-check mr-1"></i>
                                                    Graded by: {{ student.graded_by }} • {{ new Date(student.updated_at).toLocaleString() }}
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            @click="startEditGrade(student)"
                                            class="w-full px-5 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all flex items-center justify-center gap-2 font-bold shadow-lg hover:shadow-xl transform hover:scale-[1.02]"
                                        >
                                            <i class="fas fa-pen-to-square text-lg"></i>
                                            {{ student.score > 0 || student.feedback ? 'Edit Grade' : 'Grade This Student' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-12">
                                <i class="fas fa-users text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">No students assigned to this assessment yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
